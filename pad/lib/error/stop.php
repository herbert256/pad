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


  function padErrorStopCatchGo ( $error, $file, $line, $e, $org ) {

    if ( isset ( $GLOBALS ['padErrrorList'] ) )
      foreach ( $GLOBALS ['padErrrorList'] as $list )
        padErrorLog ( $list );

    padErrorLog ( $org );
    padErrorLog ( "$file:$line $error");
    padErrorLog ( $e->getFile() . ':' .  $e->getLine() . ' ERROR-STOP: ' . $e->getMessage() );

    echo "Error: " . padID ();

  }


  function padErrorStopCatchCatch ( $error, $file, $line, $e, $e2, $org ) {

    set_error_handler ( function ($s, $m, $f, $l) { throw new ErrorException ($m, 0, $s, $f, $l); } );
    error_reporting (0);

    try {   

      $file = 'oops/' . padID () . '.txt';
      
      padFilePutContents ( $file, $org, TRUE );
      padFilePutContents ( $file, "$file:$line $error", TRUE );
      padFilePutContents ( $file, $e->getFile() . ':' . $e->getLine() . ' ' . $e->getMessage(), TRUE );
      padFilePutContents ( $file, $e2->getFile() . ':' . $e2->getLine() . ' ' . $e2->getMessage(), TRUE );

    } catch (Throwable $e2 ) {

      echo 'oops';
    
    }

  }


?>