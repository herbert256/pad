<?php


  set_error_handler ( 'padErrorThrow' );

  try {

    $error2 = padErrorGet ( $e );

    padErrorLog ( $error );
    padErrorLog ( $error2 );

    padErrorExit ( "$error\n$error2" );

  } catch (Throwable $e3) {

    padErrorStopCatch ( $error, $e, $e3 );

  }
  
  padError500 ();


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
    
    padError500 ();

  }


  function padErrorStopCatchCatch ( $error1, $e2, $e3, $e4 ) {
    
    set_error_handler ( 'padErrorThrow' );

    try {

      $buffer = padEmptyBuffers ();
 
      if ( ! headers_sent () )
        header ( 'HTTP/1.0 500 Internal Server Error' );
 
      padErrorConsole ( $error1 );
      padErrorConsole ( padErrorGet ( $e2 ) );
      padErrorConsole ( padErrorGet ( $e3 ) );
      padErrorConsole ( padErrorGet ( $e4 ) );
      padErrorConsole ( $buffer );

    } catch (Throwable $e5) {

      padErrorStopCatchCatchCatch ( $error1, $e2, $e3, $e4, $e5 );

    }

    padError500 ();

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
    
    padError500 ();

  }
 

  function padErrorLog ( $info ) {

    if ( ! $info )
      return;

    set_error_handler ( 'padErrorThrow' );

    try {

      $log = '[PAD] ' . padID () . ' ' . padMakeSafe ( $info, 200 );

      error_log ( $log, 4 );
    
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

    if ( ! $info )
      return;

    set_error_handler ( 'padErrorThrow' );

    try {

      $log = padID () . ' - ' . padMakeSafe ( $info );

      padFilePutContents ( 'error_log.txt', $log, true );

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


  function padErrorExit ( $error ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      $buffer = padEmptyBuffers ();

      if ( padLocal () )
        echo "\n<pre>$error\n\n$buffer</pre>";
      else
        echo 'Error: ' . padID ();
    
    } catch (Throwable $e) {
    
      padErrorExitCatch ( $error, $e );
    
    }

    padError500 ();

  }


  function padErrorExitCatch ( $error, $e ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      padErrorConsole ( $error );
      padErrorConsole ( adErrorGet ( $e ) );
      
    } catch (Throwable $e2) {

      // Ignoring errors
  
    }

    padError500 ();

  }

  
  function padErrorConsole ( $info ) {

    if ( ! $info )
      return;

    set_error_handler ( 'padErrorThrow' );

    try {

      if ( padLocal () )
        echo "<pre>\n$info</pre>";
    
    } catch (Throwable $e) {
    
      // Ignore errors
    
    }

    restore_error_handler ();

  }


  function padError500 ( ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      if ( ! headers_sent () )
        header ( 'HTTP/1.0 500 Internal Server Error' );

      padExit ( TRUE );

    } catch (Throwable $e) {
    
      // Ignore errors
    
    }

    $GLOBALS ['padSkipShutdown'] = TRUE;
    exit;

  }


?>