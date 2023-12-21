<?php
  

  function padErrorExit ( $error ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      padErrorExitGo ( $error );
    
    } catch (Throwable $e) {
    
      padErrorExitCatch ( $error, $e );
    
    }

    $GLOBALS ['padSkipShutdown'] = TRUE;

    exit;

  }


  function padErrorExitGo ( $error ) {
    
    padEmptyBuffers ();

    if ( ! headers_sent () )
      header ( 'HTTP/1.0 500 Internal Server Error' );

    if ( padLocal () )
      echo "\n<pre>$error</pre>";
    else
      echo 'Error: ' . padID ();
  
  }


  function padErrorExitCatch ( $error, $e ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      padErrorConsole ( $error );
      padErrorConsole ( $e->getFile() . ':' .  $e->getLine() . ' EXIT-CATCH: ' . $e->getMessage() );
      
    } catch (Throwable $e2) {

      // Ignore errors
  
    }

    restore_error_handler ();

  }


?>