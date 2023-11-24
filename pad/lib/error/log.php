<?php
  

  function padErrorLog ( $info ) {

    if ( ! $info )
      return;

    set_error_handler ( 'padErrorThrow' );

    try {

      padErrorLogGo ( $info );
    
    } catch (Throwable $e) {
    
      padErrorLogCatch ( $info, $e );
    
    }

    restore_error_handler ();

  }


  function padErrorLogGo ( $info ) {

    $log = '[PAD] ' . padID () . ' ' . padMakeSafe ( $info, 200 );

    if ( padErrorLogCheck ( 'log', $log )  )
      error_log ( $log, 4 );

  }


  function padErrorLogCatch ( $error, $e ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      padErrorLogFile ( $error );
      padErrorLogFile ( $e->getFile() . ':' .  $e->getLine() . ' LOG-ERROR: ' . $e->getMessage() );
    
    } catch (Throwable $e2) {

      // padErrorLogCatchCatch ( $error, $e, $e2 );
  
    }

    restore_error_handler ();

  }

  function padErrorLogCatchCatch ( $error1, $e2, $e3 ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      $error2 = $e2->getFile() . ':' . $e2->getLine() . $e3->getMessage();
      $error3 = $e3->getFile() . ':' . $e3->getLine() . $e3->getMessage();

      padErrorExit ( "Error log not working\n\n$error1\n$error2\n$error3" );

    } catch ( Throwable $ignored ) {

      echo 'oops';

      $GLOBALS ['padSkipShutdown']     = TRUE;
      $GLOBALS ['padSkipBootShutdown'] = TRUE;
      
      exit;

    }

  }

  function padErrorLogFile ( $info ) {

    $log = padID () . ' - ' . padMakeSafe ( $info );

    if ( padErrorLogCheck ( 'file', $log ) )
      padFilePutContents ( 'error_log.txt', $log, true );

  }


  function padErrorLogCheck ( $type, $info ) {

    $md5 = md5 ( trim($info) );

    if ( isset ( $GLOBALS["padErrorCheck_$type"] ) and isset ( $GLOBALS["padErrorCheck_$type"] [$md5] ) )
      return FALSE;

    $GLOBALS["padErrorCheck_$type"] [$md5] = TRUE;

    return TRUE;

  }


?>