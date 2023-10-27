<?php
  

  function padErrorLog ( $info ) {

    if ( ! $info )
      return;

    set_error_handler ( function ($s, $m, $f, $l) { throw new ErrorException ($m, 0, $s, $f, $l); } );
    $reporting = error_reporting (0);

    try {

      padErrorLogGo ( $info );
    
    } catch (Throwable $e) {
    
      padErrorLogCatch ( $info, $e );
    
    }

    error_reporting ($reporting);
    restore_error_handler ();

  }


  function padErrorLogGo ( $info ) {

    $log = '[PAD] ' . padID () . ' - ' . padMakeSafe ( $info );

    if ( ! padErrorLogCheck ( 'log', $log )  )
      error_log ( $log, 4 );

  }


  function padErrorLogCatch ( $info, $e ) {

    set_error_handler ( function ($s, $m, $f, $l) { throw new ErrorException ($m, 0, $s, $f, $l); } );
    $reporting = error_reporting (0);

    try {

      padFilePutContents ( 'error_log.txt', padID () . ' - ' . $info,  true );

      $log = $e->getFile() . ':' .  $e->getLine() . ' LOG-ERROR: ' . $e->getMessage();

      if ( ! padErrorLogCheck ( 'catch', $log ) ) 
        padFilePutContents ( 'error_log.txt', padID () . ' - ' . $log, true );
    
    } catch (Throwable $ignored) {
  
    }

    error_reporting ($reporting);
    restore_error_handler ();

  }


?>