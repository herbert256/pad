<?php

  // The boot-time error net: installed by start/pad.php before config, lib or the engine
  // exist, so a failure there still gives a controlled 500 instead of a blank page.
  //
  // Saves the previous settings in $padDisplayErrors / $padErrorReporting (error/types/php.php
  // restores them) and registers padBootHandler, padBootException and padBootShutdown, plus
  // padBootError for a deliberate abort; all four funnel into padBootStop.
  //
  // padBootStop first offers the failure to padClaudeError (error/claude.php), then discards
  // every output buffer, sends 500 and prints the message via padShowErrorLocal - or, off-box,
  // logs it and echoes only a request id (padShowErrorRemote). A failure while doing that lands
  // in padBootStopCatch / padBootProblems; padBootExit ends the request through padExit if lib
  // is loaded, else through exits/exit.php. $padBootShutdown marks the net as spent, so the
  // shutdown hook stays quiet once padErrorRestoreBoot or a normal exit has run.
  //
  // padLocal, defined here, is the engine-wide "CLI or this machine's own request" test.

  $padDisplayErrors  = ini_set ('display_errors', 0);
  $padErrorReporting = error_reporting (E_ALL);

  // Room for the report of a request that ran out of memory: freed by padBootRoom.

  $padBootRoom = str_repeat ( ' ', 65536 );

  set_error_handler          ( 'padBootHandler'   );
  set_exception_handler      ( 'padBootException' );
  register_shutdown_function ( 'padBootShutdown'  );

  function padBootError ( $error ) {

    extract ( debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS, 1) [0] );

    padBootStop ( $error, $file, $line );

  }

  function padBootHandler ( $type, $error, $file, $line ) {

    // The @ contract holds here too: a suppressed call empties error_reporting for its
    // duration, and a handler that ignores that turns a tolerated failure - the deleter's
    // quiet rmdir attempts first of all - into a fatal only under the boot action.

    if ( ! ( error_reporting () & $type ) )
      return TRUE;

    padBootStop ( $error, $file, $line );

  }

  function padBootException ( $error ) {

    padBootStop ( $error->getMessage(), $error->getFile(), $error->getLine() );

  }

  function padBootShutdown () {

    global $padBootShutdown;

    if ( isset ( $padBootShutdown ) )
      return;

    $error = error_get_last ();

    if ( $error !== NULL ) {
      padBootRoom ( $error );
      padBootStop ( $error['message'], $error['file'], $error['line'] );
    }

  }

  // A fatal for memory leaves the shutdown hook none to report it with: the report died
  // on its first allocation, the visitor got an empty 500 and the log nothing. The reserve
  // goes, and the limit is raised for the report alone - the request is over.

  function padBootRoom ( $error ) {

    unset ( $GLOBALS ['padBootRoom'] );

    if ( str_starts_with ( (string) ( $error ['message'] ?? '' ), 'Allowed memory size' ) )
      @ini_set ( 'memory_limit', (string) ( memory_get_usage () + 64 * 1048576 ) );

  }

  function padBootStop ( $error, $file, $line ) {

    // The application's _events/error.php hears it first - once the application is
    // resolved; a failure before that is the engine's alone (lib/events.php).

    if ( function_exists ( 'padEventError' ) )
      padEventError ( $error, $file, $line );

    padClaudeError ( $error, $file, $line );

    global $padBootShutdown;

    $padBootShutdown = TRUE;

    set_error_handler ( 'padBootStopError' );

    try {

      padBootStopTry ( $error, $file, $line );
      restore_error_handler ();

    } catch (Throwable $e) {

      restore_error_handler ();
      padBootStopCatch ( "$file:$line $error", $e );

    }

    padBootExit ();

  }

  function padBootStopTry ( $error, $file, $line ) {

    global $padBootStop;

    if ( isset ( $padBootStop ) )
      padBootProblems ( "$file:$line $error", $padBootStop );

    $padBootStop = "$file:$line $error";

    $j = ob_get_level ();
    for ( $i = 1; $i <= $j; $i++ )
      ob_get_clean ();

    // A fatal has had PHP set the 500 already, and a second http_response_code warns - the
    // warning ended the report before the remote visitor's id was logged or shown.

    if ( ! headers_sent () and http_response_code () != 500 )
      http_response_code(500);

    if     ( padAnswerSent () ) padShowErrorLog    ( $error, $file, $line );
    elseif ( padLocal ()      ) padShowErrorLocal  ( $error, $file, $line );
    else                        padShowErrorRemote ( $error, $file, $line );

  }

  // The template position goes under the message once lib is there to find it - the
  // error lies in a template more often than in the engine line that noticed it.

  function padShowErrorLocal ( $error, $file, $line ) {

    $where = function_exists ( 'padErrorTemplate' ) ? padSrcReport ( padErrorTemplate ( (string) $error ) ) : '';
    $where = $where ? "\n\n$where" : '';

    if ( PHP_SAPI === 'cli' ) {

      echo "$file:$line $error$where\n";

    } else {

      $msg = htmlspecialchars("$file:$line $error$where", ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

      echo "\n<pre>{$msg}</pre>";

    }

  }

  function padShowErrorRemote ( $error, $file, $line ) {

      echo 'Error: ' . padShowErrorLog ( $error, $file, $line );

  }

  function padShowErrorLog ( $error, $file, $line ) {

      global $padReqID;

      $id = $padReqID ?? bin2hex(random_bytes(8));

      // One line per error, whatever it quotes: the message can carry request text - a
      // page name is the query key as sent - and a newline in it wrote a second, forged
      // [PAD] line. Control characters become spaces here, before lib's padMakeSafe exists.

      $error = preg_replace ( '/[\x00-\x1F\x7F]+/', ' ', (string) $error );

      $where = ( $file !== '' ) ? "$file:$line " : '';

      error_log ( "[PAD] $id $where$error", 4 );

      return $id;

  }

  function padBootStopCatch  ( $error, $e ) {

    set_error_handler ( 'padBootStopError' );

    try {

      $error2 = $e->getFile() . ':' .  $e->getLine() . ' ' . $e->getMessage() ;

      padBootProblems( $error2, $error );

    } catch (Throwable $e2) {

    }

    include PAD . 'exits/exit.php';

  }

  function padBootStopError ( $severity, $message, $filename, $lineno ) {

    throw new ErrorException ( $message, 0, $severity, $filename, $lineno );

  }

  // A visitor from elsewhere gets the request id here too, and the log both messages: the
  // report that failed was the one that would have logged them.

  function padBootProblems ( $error1, $error2 ) {

    if ( padLocal () and ! padAnswerSent () )
      echo '<pre><br>' . htmlspecialchars ( "$error2", ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' )
         . '<br>'     . htmlspecialchars ( "$error1", ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' ) . '</pre>';
    elseif ( ! padLocal () ) {
      $id = padShowErrorLog ( $error2, '', '' );
      padShowErrorLog ( $error1, '', '' );
      if ( ! padAnswerSent () )
        echo "Error: $id";
    }

    padBootExit ();

  }

  function padBootExit () {

    if ( function_exists ( 'padExit') )
      padExit ( 500 );
    
    include PAD . 'exits/exit.php';

  }

  // Local is the command line, or a request this machine made to itself (padLoopback in
  // error/claude.php). It read SERVER_NAME too, which Apache fills from the client's Host
  // header, so any visitor sending Host: localhost got the full report with the database
  // passwords; and it trusted a developer host name written into the list. Behind
  // $padDiagnostics, read before config exists as the default TRUE.

  // Whether the response has gone out: over HTTP its body and Content-Length are on the
  // wire, and a report echoed after them lands behind the declared length - a client that
  // reuses the connection reads it as the start of its next response. An error then goes
  // to the log and the report on disk, never to the output. The command line has no such
  // framing, and a late error printed there is still worth reading.

  function padAnswerSent () {

    return isset ( $GLOBALS ['padSent'] ) and PHP_SAPI != 'cli';

  }

  function padLocal () {

    if ( PHP_SAPI === 'cli' )
      return TRUE;

    if ( padDiagnosticsOff () )
      return FALSE;

    return padLoopback ();

  }

  // $padDiagnostics says no: FALSE, or any value that means it - a 0 in the configuration,
  // the '0' or 'off' an .env holds when the setting is read through padEnv. Only the boolean
  // counted, and every other no left the full reports on.

  function padDiagnosticsOff () {

    $value = $GLOBALS ['padDiagnostics'] ?? TRUE;

    return ! $value or ( is_string ( $value ) and in_array ( strtolower ( trim ( $value ) ), [ 'off', 'no', 'false' ], TRUE ) );

  }

?>
