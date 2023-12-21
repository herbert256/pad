<?php
  

  function padErrorFile ( $info ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      $log = padID () . ' - ' . padMakeSafe ( $info );

      if ( padErrorCheck ( 'file', $log ) )
        padFilePutContents ( 'error_log.txt', $log, true );

    } catch (Throwable $e) {

      padErrorFileCatch ( $e, $info );
  
    }

  }


  function padErrorFileCatch ( $e, $info ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      padErrorConsole ( $info );
      padErrorConsole ( $e->getFile() . ':' .  $e->getLine() . ' FILE-CATCH: ' . $e->getMessage() );

    } catch (Throwable $e2) {

      // Ignore errors
  
    }

  }


?>