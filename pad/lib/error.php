<?php

  // The last-resort error path. Reporting an error may itself fail, so this file is built
  // as a ladder of fallbacks: each step wraps its work in set_error_handler(padErrorThrow)
  // and a try, and hands a further failure down to the next, quieter step. Whatever
  // happens, the request ends through padExit(500) rather than dying mid-page.
  //
  // padError        what applications and the engine call. It records the caller's file
  //                 and line and hands over to padErrorGo (padErrorAt: the same, for a
  //                 spot in the template other than the current tag), whose definition comes from
  //                 error/types/<$padErrorAction>.php (pad, boot, php, stop, exit, ignore,
  //                 log, dump) - that is how the configured error action is selected. It
  //                 always returns FALSE, so `return padError(...)` reads naturally
  // padErrorThrow   converts a PHP warning or notice into an ErrorException, respecting
  //                 the current error_reporting mask; installed around risky sections
  // padErrorGet     file:line message of a Throwable, the standard one-line form
  //
  // The reporting ladder, most to least capable: padErrorStop (with its Try/Catch and the
  // deeper padErrorStopCatch, ...CatchCatch, ...CatchCatchCatch steps), padErrorLog to the
  // SAPI log, padErrorFile appending to DATA/error_log.txt, padErrorConsole echoing to the
  // page, and padErrorExit which flushes the buffers and prints. Detail is only shown when
  // padLocal() says the request is local; otherwise the visitor gets the request id alone.
  //
  // padErrorRestoreBoot drops PAD's handlers and lets the boot handler take over again.

  function padErrorThrow ( $type, $error, $file, $line ) {

    if ( ( error_reporting() & $type ) )
      throw new \ErrorException ( $error, 0, $type, $file, $line);

  }

  function padError ($error) {

    extract ( debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS, 1) [0] );

    padErrorGo ( 'PAD: ' . $error, $file, $line );

    return FALSE;

  }

  // padError for a spot that is not the tag being worked on - an unclosed { further on, an
  // @word@ in the built page, a second @page@ in a wrapper. $where says where, for the
  // template position of the report (lib/source.php): [ 'level', 'out' ] a position in a
  // level's working text, [ 'level', 'base' ] one in its base, [ 'file', 'pos' ] one in a
  // template file, [ 'search' ] a text that stands in one template only; 'length' how much
  // of it to mark. It holds for this error only - the
  // dump and log actions carry on to the next.

  function padErrorAt ( $error, $where ) {

    global $padErrorAt;

    extract ( debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS, 1) [0] );

    $padErrorAt = $where;

    try {
      padErrorGo ( 'PAD: ' . $error, $file, $line );
    } finally {
      $padErrorAt = NULL;
    }

    return FALSE;

  }

  function padErrorRestoreBoot () {

    global $padBootShutdown;

    restore_error_handler ();
    restore_exception_handler ();
    $padBootShutdown = TRUE;

  }

  function padErrorGet ( $e ) {

    return $e->getFile() . ':' .  $e->getLine() . ' ' . $e->getMessage() ;

  }

  function padErrorStop ( $error, $e ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      padErrorStopTry ( $error, $e );

    } catch (Throwable $e2) {

      padErrorStopCatch ( $error, $e, $e2 );

    }

    restore_error_handler ();

    padExit ( 500 );

  }

  function padErrorStopTry ( $error, $e ) {

    $error2 = padErrorGet ( $e );

    padErrorLog ( $error );
    padErrorLog ( $error2 );

    padErrorExit ( "$error\n$error2" );

  }

  function padErrorStopCatch ( $error1, $e2, $e3 ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      $error2 = padErrorGet ( $e2 );
      $error3 = padErrorGet ( $e3 );

      padErrorFile ( $error1 );
      padErrorFile ( $error2 );
      padErrorFile ( $error3 );

      padErrorExit ( "$error1\n$error2\n$error3" );

    } catch (Throwable $e4) {

      padErrorStopCatchCatch ( $error1, $e2, $e3, $e4 );

    }

    restore_error_handler ();

    padExit ( 500 );

  }

  function padErrorStopCatchCatch ( $error1, $e2, $e3, $e4 ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      if ( ! headers_sent () )
        header ( 'HTTP/1.0 500 Internal Server Error' );

      $error2 = padErrorGet ( $e2 );
      $error3 = padErrorGet ( $e3 );
      $error4 = padErrorGet ( $e4 );

      padErrorConsole ( $error1 );
      padErrorConsole ( $error2 );
      padErrorConsole ( $error3 );
      padErrorConsole ( $error4 );

      padErrorExit ( "$error1\n$error2\n$error3\n$error4" );

    } catch (Throwable $e5) {

      padErrorStopCatchCatchCatch ( $error1, $e2, $e3, $e4, $e5 );

    }

    restore_error_handler ();

    padExit ( 500 );

  }

  function padErrorStopCatchCatchCatch ( $error1, $e2, $e3, $e4, $e5 ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      $error2 = padErrorGet ( $e2 );
      $error3 = padErrorGet ( $e3 );
      $error4 = padErrorGet ( $e4 );
      $error5 = padErrorGet ( $e5 );

      padErrorExit ( "$error1\n$error2\n$error3\n$error4\n$error5" );

    } catch (Throwable $e6) {

    }

    restore_error_handler ();

    padExit ( 500 );

  }

  function padErrorLog ( $info ) {

    if ( ! $info )
      return;

    set_error_handler ( 'padErrorThrow' );

    try {

      padLogError ( $info );

    } catch (Throwable $e) {

      padErrorLogCatch ( $info, $e );

    }

    restore_error_handler ();

  }

  function padErrorLogCatch ( $error, $e2 ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      padErrorFile ( $error );
      padErrorFile ( padErrorGet ( $e2 ) );

    } catch ( Throwable $e3 ) {

    }

    restore_error_handler ();

  }

  function padErrorFile ( $info ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      $log = padID () . ' - ' . padMakeSafe ( $info );

      padFilePut ( 'error_log.txt', $log, true );

    } catch (Throwable $e) {

      padErrorFileCatch ( $e, $info );

    }

    restore_error_handler ();

  }

  function padErrorFileCatch ( $e, $info ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      padErrorConsole ( $info );
      padErrorConsole ( padErrorGet ( $e ) );

    } catch (Throwable $e2) {

    }

    restore_error_handler ();

  }

  function padErrorConsole ( $info ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      if ( padLocal () )
        echo "<pre>\nError: " . htmlspecialchars ( "$info", ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' ) . "</pre>";
      else
        echo '<pre>Unknown error occurred.</pre>';

    } catch (Throwable $e) {

    }

    restore_error_handler ();

  }

  function padErrorExit ( $error ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      padEmptyBuffers ( $buffer );

      if ( padLocal () )
        echo "\n<pre>" . htmlspecialchars ( "$error", ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' ) . "\n\n$buffer</pre>";
      else
        echo 'Error: ' . padID ();

    } catch (Throwable $e) {

      padErrorExitCatch ( $error, $e );

    }

    restore_error_handler ();

    padExit ( 500 );

  }

  function padErrorExitCatch ( $error, $e ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      padErrorConsole ( $error );
      padErrorConsole ( padErrorGet ( $e ) );

    } catch (Throwable $e2) {

      echo 'oops';

    }

    restore_error_handler ();

    padExit ( 500 );

  }

?>