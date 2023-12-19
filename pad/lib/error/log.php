<?php
  

  function padErrorLog ( $info ) {

    if ( ! $info )
      return;

    set_error_handler ( 'padErrorThrow' );

    try {

      $log = '[PAD] ' . padID () . ' ' . padMakeSafe ( $info, 200 );

      if ( padErrorLogCheck ( 'log', $log )  )
        error_log ( $log, 4 );
    
    } catch (Throwable $e) {
    
      padErrorLogCatch ( $info, $e );
    
    }

    restore_error_handler ();

  }


  function padErrorLogCatch ( $error, $e ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      padErrorLogFile ( $error );
      padErrorLogFile ( $e->getFile() . ':' .  $e->getLine() . ' LOG-ERROR: ' . $e->getMessage() );
    
    } catch (Throwable $e2) {

      padErrorLogCatchCatch ( $error, $e, $e2 );
  
    }

    restore_error_handler ();

  }


  function padErrorLogCatchCatch ( $e1, $e2, $e3 ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      if ( padLocal () ) {
        padErrorConsole ( $e1 );
        padErrorConsole ( $e2->getFile() . ':' .  $e2->getLine() . ' LOG-ERROR: ' . $e2->getMessage() );
        padErrorConsole ( $e3->getFile() . ':' .  $e3->getLine() . ' LOG-ERROR-CATCH: ' . $e3->getMessage() );
      }
    
    } catch (Throwable $e2) {

      // Ignore errors
  
    }

    restore_error_handler ();

  }


  function padErrorLogFile ( $info ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      $log = padID () . ' - ' . padMakeSafe ( $info );

      if ( padErrorLogCheck ( 'file', $log ) )
        xpadFilePutContents ( 'error_log.txt', $log, true );

    } catch (Throwable $e) {

      padErrorLogFileCatch ( $e, $info );
  
    }

  }


  function padErrorLogFileCatch ( $e, $info ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      padErrorConsole ( $info );
      padErrorConsole ( $e->getFile() . ':' .  $e->getLine() . ' FILE-CATCH: ' . $e->getMessage() );

    } catch (Throwable $e2) {

      // Ignore errors
  
    }

  }



  function padErrorLogCheck ( $type, $info ) {

    $md5 = md5 ( trim($info) );

    if ( isset ( $GLOBALS["padErrorCheck_$type"] ) and isset ( $GLOBALS["padErrorCheck_$type"] [$md5] ) )
      return FALSE;

    $GLOBALS["padErrorCheck_$type"] [$md5] = TRUE;

    return TRUE;

  }


?>