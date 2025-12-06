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

    if ( isset ( $GLOBALS ['padBootShutdown'] ) )
      return;

    $error = error_get_last ();
 
    if ($error !== NULL)
      padBootStop ( $error['message'], $error['file'], $error['line'] );
 
  }


  function padBootStop ( $error, $file, $line ) {

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

    if ( isset ( $GLOBALS ['padBootStop'] ) ) 
      padBootProblems ( "$file:$line $error", $GLOBALS ['padBootStop'] );
  
    $GLOBALS ['padBootStop'] = "$file:$line $error";
      
    $j = ob_get_level (); 
    for ( $i = 1; $i <= $j; $i++ ) 
      ob_get_clean ();

    if ( ! headers_sent () )
      header ( 'HTTP/1.0 500 Internal Server Error' );

    if ( padLocal () )
 
      echo "\n<pre>$file:$line $error</pre>";
 
    else {

      $id = $GLOBALS ['padReqID'] ?? uniqid (TRUE);
      error_log ( "[PAD] $id $file:$line $error", 4 );
      echo "Error: $id";
 
    }
 
  }




  function padBootStopCatch  ( $error, $e ) {
    
    set_error_handler ( 'padBootStopError' );

    try {

      $error2 = $e->getFile() . ':' .  $e->getLine() . ' ' . $e->getMessage() ;

      padBootProblems( $error2, $error );

    } catch (Throwable $e2) {

      // Ignore Errors


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


?>