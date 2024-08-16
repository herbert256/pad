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

    if ( isset ( $GLOBALS ['padSkipShutdown'] ) or isset ( $GLOBALS ['padSkipBootShutdown'] ) )
      return;

    $error = error_get_last ();
 
    if ($error !== NULL)
      padBootStop ( $error['message'], $error['file'], $error['line'] );
 
  }


  function padBootStop ( $error, $file, $line ) {
      
    set_error_handler ( 'padBootStopError' );

    try {

      padBootStopGo ( $error, $file, $line );

    } catch (Throwable $e) {

      padBootStopCatch ();;
  
    }

    exit;

  }


  function padBootStopError ( $severity, $message, $filename, $lineno ) {

    throw new ErrorException ( $message, 0, $severity, $filename, $lineno );

  }


  function padBootStopGo ( $error, $file, $line ) {

    $GLOBALS ['padSkipShutdown'] = $GLOBALS ['padSkipBootShutdown'] = TRUE;

    $j = ob_get_level (); 
    for ( $i = 1; $i <= $j; $i++ ) 
      ob_get_clean ();

    if ( ! headers_sent () )
      header ( 'HTTP/1.0 500 Internal Server Error' );

    if ( padLocal () )
 
      echo "\n<pre>$file:$line $error</pre>";
 
    else {

      $id = $GLOBALS ['padReqID'] ?? uniqid (TRUE);
      error_log ( "[PAD] $id - $file:$line $error", 4 );
      echo "Error: $id";
 
    }
 
    exit;

  }


  function padBootStopCatch () {

    echo 'oops';
    exit;

  }


  function padLocal () {

    $local = [ 'localhost', 'penguin.linux.test', '127.0.0.1' ];

    $server = [ 
      $_SERVER ['HTTP_HOST']   ?? '',  
      $_SERVER ['REMOTE_ADDR'] ?? '', 
      $_SERVER ['SERVER_NAME'] ?? ''
    ];

    foreach ( $local as $check )
      if ( in_array( $check, $server) )
        return TRUE;

    return FALSE;
    
  }


  function padErrorThrow ( $type, $error, $file, $line ) {

    if ( ( error_reporting() & $type ) ) 
      throw new \ErrorException ( $error, 0, $type, $file, $line);

  }


?>