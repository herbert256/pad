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

    if ( isset ( $GLOBALS ['padSkipShutdown'] ) )
      return;

    $error = error_get_last ();
 
    if ($error !== NULL)
      padBootStop ( $error['message'], $error['file'], $error['line'] );
 
  }


  function padBootStop ( $error, $file, $line ) {

    if ( isset ( $GLOBALS ['padBootStop'] ) ) 
      padBootExit ( "$file:$line $error", $GLOBALS ['padBootStop'] );
  
    $GLOBALS ['padBootStop'] = "$file:$line $error";
      
    set_error_handler ( 'padBootStopError' );

    try {

      padBootStopGo ( $error, $file, $line );

    } catch (Throwable $e) {

      padBootStopCatch ( "$file:$line $error", $e );
  
    }

    padBootExit ();

  }


  function padBootStopGo ( $error, $file, $line ) {

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


  function padBootStopError ( $severity, $message, $filename, $lineno ) {

    throw new ErrorException ( $message, 0, $severity, $filename, $lineno );

  }


  function padBootStopCatch  ( $error, $e ) {
    
    set_error_handler ( 'padErrorThrow' );

    try {

      $error2 = $e->getFile() . ':' .  $e->getLine() . ' ' . $e->getMessage() ;

      padBootExit ( $error2, $error );

    } catch (Throwable $e2) {

      // Ignore Errors


    }

    include 'exits/exit.php';

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


  function padBootExit ( $error1 = '', $error2 = '' ) {

    if ( padLocal () and ( $error1 or $error2 ) )
      echo "<pre><br>$error2<br>$error1</pre>";

    if ( function_exists ( 'padExit') )
      padExit ( 500 );
    else
      include 'exits/exit.php';

  }


?>