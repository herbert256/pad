<?php
  

  function padErrorStop ( $error, $file, $line, $org='' ) {

    set_error_handler ( function ($s, $m, $f, $l) { throw new ErrorException ($m, 0, $s, $f, $l); } );
    error_reporting (0);

    try {

      padErrorStopGo ( $error, $file, $line, $org );
    
    } catch (Throwable $e) {
    
      padErrorStopCatch ( $error, $file, $line, $e, $org );
    
    }

    $GLOBALS ['padSkipShutdown']     = TRUE;
    $GLOBALS ['padSkipBootShutdown'] = TRUE;

    exit;

  }


  function padErrorStopGo ( $error, $file, $line, $org ) {

    if ( ! isset ( $GLOBALS ['padDumpToDir'] ) )
      if ( $org )
        padDumpToDir ( "$org\n\n$file:$line $error" );
      else
        padDumpToDir ( "$file:$line $error" );

    if ( $GLOBALS ['padErrorLog'] ) {
      padErrorLog ( $org );
      padErrorLog ( "$file:$line $error" );
    }

    if ( isset ( $GLOBALS ['padErrrorList'] ) )
      foreach ( $GLOBALS ['padErrrorList'] as $list )
        $error .= "\n" . $list;

    if ( $org )
      $error .= "\n" . $org;

    padBootStop ( $error, $file, $line );

  }


  function padErrorStopCatch ( $error, $file, $line, $e, $org ) {

    set_error_handler ( function ($s, $m, $f, $l) { throw new ErrorException ($m, 0, $s, $f, $l); } );
    error_reporting (0);

    try {   

      padErrorStopCatchGo ( $error, $file, $line, $e, $org );

    } catch (Throwable $e2 ) {

      padErrorStopCatchCatch ( $error, $file, $line, $e, $e2, $org );
    
    }

  }


  function padErrorStopCatchGo ( $error, $file, $line, $e, $error1 ) {

    if ( isset ( $GLOBALS ['padErrrorList'] ) )
      foreach ( $GLOBALS ['padErrrorList'] as $list )
        padErrorLog ( $list );

    $error2 = "$file:$line $error";
    $error3 = $e->getFile() . ':' .  $e->getLine() . ' ERROR-STOP: ' . $e->getMessage() ;

    padErrorLog ( $error1 );
    padErrorLog ( $error2 );
    padErrorLog ( $error3 );

    padErrorExit ( "$error1\n$error2\n$error3" );

  }


  function padErrorStopCatchCatch ( $error, $file, $line, $e1, $e2, $org ) {

    set_error_handler ( function ($s, $m, $f, $l) { throw new ErrorException ($m, 0, $s, $f, $l); } );
    error_reporting (0);

    try {   

      padErrorLogFile ( $org );
      padErrorLogFile ( "$file:$line $error" );
      padErrorLogFile ( $e1->getFile() . ':' . $e1->getLine() . ' ' . $e1->getMessage() );
      padErrorLogFile ( $e2->getFile() . ':' . $e2->getLine() . ' ' . $e2->getMessage() );

      padErrorExit ( 'Internal error, see error_log.txt' );

    } catch (Throwable $ignored ) {

      padErrorStopCatchCatchCatch ();
    
    }

  }


  function padErrorStopCatchCatchCatch ( ) {

    echo 'oops (2)';

    $GLOBALS ['padSkipShutdown']     = TRUE;
    $GLOBALS ['padSkipBootShutdown'] = TRUE;

    exit;

  }


?>