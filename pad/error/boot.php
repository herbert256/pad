<?php

  $padDisplayErrors  = ini_set ('display_errors', 0);
  $padErrorReporting = error_reporting (E_ALL);

  set_error_handler          ( 'padBootHandler'   );
  set_exception_handler      ( 'padBootException' );
  register_shutdown_function ( 'padBootShutdown'  );

  function padBootError ( $error ) {

    extract ( debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS, 1) [0] );

    padBootStop ( $error, $file, $line );

  }

  function padBootHandler ( $type, $error, $file, $line ) {

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

    if ( $error !== NULL )
      padBootStop ( $error['message'], $error['file'], $error['line'] );

  }

  function padBootStop ( $error, $file, $line ) {

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

    if ( ! headers_sent () )
      http_response_code(500);

    if ( padLocal () )  padShowErrorLocal  ( $error, $file, $line );
    else                padShowErrorRemote ( $error, $file, $line );

  }

  function padShowErrorLocal ( $error, $file, $line ) {

    if ( PHP_SAPI === 'cli' ) {

      echo "$file:$line $error\n";

    } else {

      $msg = htmlspecialchars("$file:$line $error", ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

      echo "\n<pre>{$msg}</pre>";

    }

  }

  function padShowErrorRemote ( $error, $file, $line ) {

      global $padReqID;

      $id = $padReqID ?? bin2hex(random_bytes(8));

      error_log ( "[PAD] $id $file:$line $error", 4 );

      echo "Error: $id";

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

  function padBootProblems ( $error1, $error2 ) {

    if ( padLocal () )
      echo "<pre><br>$error2<br>$error1</pre>";

    padBootExit ();

  }

  function padBootExit () {

    if ( function_exists ( 'padExit') )
      padExit ( 500 );
    else
      include PAD . 'exits/exit.php';

  }

  function padLocal () {

    if ( PHP_SAPI === 'cli' )
      return true;

    $local  = [ 'localhost', 'penguin.linux.test', '127.0.0.1', '::1' ];
    $server = [ $_SERVER ['REMOTE_ADDR'] ?? '',  $_SERVER ['SERVER_NAME'] ?? '' ];

    foreach ( $local as $check )
      if ( in_array( $check, $server) )
        return TRUE;

    return FALSE;

  }

?>