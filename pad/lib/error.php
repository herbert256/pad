<?php


  function padErrorThrow ( $type, $error, $file, $line ) {

    if ( ( error_reporting() & $type ) ) 
      throw new \ErrorException ( $error, 0, $type, $file, $line);

  }


  function padError ($error) {
 
    extract ( debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS, 1) [0] );

    padErrorGo ( 'PAD: ' . $error, $file, $line ); 

    return FALSE; 
 
  }


  function padErrorRestoreBoot () {

    restore_error_handler ();
    restore_exception_handler ();
    $GLOBALS ['padBootShutdown'] = TRUE;

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

      // Ignoring errors

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

      // Ignore errors
  
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

      // Ignore errors
  
    }

    restore_error_handler ();

  }

  
  function padErrorConsole ( $info ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      if ( padLocal () )
        echo "<pre>\nError: $info</pre>";
      else
        echo '<pre>Unknow error occurred.</pre>';
    
    } catch (Throwable $e) {
    
      // Ignore errors
    
    }

    restore_error_handler ();

  }


  function padErrorExit ( $error ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      padEmptyBuffers ( $buffer );

      if ( padLocal () )
        echo "\n<pre>$error\n\n$buffer</pre>";
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