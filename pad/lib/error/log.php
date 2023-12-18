<?php
  

  function padErrorLog ( $info ) {

    if ( ! $info )
      return;

    set_error_handler ( 'padErrorThrow' );

    try {

      $log = '[PAD] ' . padID () . ' ' . padMakeSafe ( $info, 200 );

      if ( padErrorLogCheck ( 'log', $log )  )
        xerror_log ( $log, 4 );
    
    } catch (Throwable $e) {
    
      padErrorLogCatch ( $info, $e );
    
    }

    restore_error_handler ();

  }


  function padErrorLogCatch ( $error, $e ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      xpadErrorLogFile ( $error );
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

        echo "<pre>\n\n";
        echo "$e1\n\n";
        echo $e2->getFile() . ':' .  $e2->getLine() . ' LOG-ERROR: ' . $e2->getMessage() . "\n\n";
        echo $e3->getFile() . ':' .  $e3->getLine() . ' LOG-ERROR: ' . $e3->getMessage() . "\n\n";
        echo "</pre>";

        $GLOBALS ['padNoEmptyBuffers'];

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
        padFilePutContents ( 'error_log.txt', $log, true );

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