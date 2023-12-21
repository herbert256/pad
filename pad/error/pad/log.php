<?php
  

  function padErrorLog ( $info ) {

    if ( ! $info )
      return;

    set_error_handler ( 'padErrorThrow' );

    try {

      $log = '[PAD] ' . padID () . ' ' . padMakeSafe ( $info, 200 );

      if ( padErrorCheck ( 'log', $log )  )
        error_log ( $log, 4 );
    
    } catch (Throwable $e) {
    
      padErrorLogCatch ( $info, $e );
    
    }

    restore_error_handler ();

  }


  function padErrorLogCatch ( $error, $e ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      padErrorFile ( $error );
      padErrorFile ( $e->getFile() . ':' .  $e->getLine() . ' LOG-ERROR: ' . $e->getMessage() );
    
    } catch (Throwable $e2) {

      padErrorLogCatchCatch ( $error, $e, $e2 );
  
    }

    restore_error_handler ();

  }


  function padErrorLogCatchCatch ( $e1, $e2, $e3 ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      if ( ! padLocal () ) 
        return;

      $error1 = $e1; 
      $error2 = $e2->getFile() . ':' .  $e2->getLine() . ' LOG-ERROR: '       . $e2->getMessage();
      $error3 = $e3->getFile() . ':' .  $e3->getLine() . ' LOG-ERROR-CATCH: ' . $e3->getMessage();

      padErrorConsole ( $error1 );
      padErrorConsole ( $error2 );
      padErrorConsole ( $error3 );

    } catch (Throwable $e2) {

      // Ignore errors
  
    }

    restore_error_handler ();

  }


?>