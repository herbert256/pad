<?php
  

  function padErrorGo ($error, $file, $line) {

    if ( $GLOBALS['padExit'] <> 1 )
      padErrorStop ( "ERROR-SECOND: $error", $file, $line);

    $GLOBALS['padExit'] = 2;
    
    set_error_handler ( function ($s, $m, $f, $l) { throw new ErrorException ($m, 0, $s, $f, $l); } );
    $reporting = error_reporting (0);

    try {
 
      $error = "$file:$line " . padMakeSafe ( $error );

      if ( $GLOBALS ['padTrace'] )
        include pad . 'trace/error.php';    

      $GLOBALS ['padErrrorList'] [] = $error; 

      $GLOBALS['padErrorFile']  = $file;
      $GLOBALS['padErrorLine']  = $line;

      if ( $GLOBALS ['padErrorLog'] or $GLOBALS ['padErrorAction'] == 'report' )
        padErrorLog ( $error );

      if ( $GLOBALS ['padErrorReport'] or $GLOBALS ['padErrorAction'] == 'report' )
        padDumpToDir ( $error );

      if ( $GLOBALS ['padErrorAction'] == 'exit') {
        padHeader ('HTTP/1.0 500 Internal Server Error' );
        padExit ();
      }

      if ( $GLOBALS ['padErrorAction'] == 'stop' )
        padStop ( 500 );

      if ( $GLOBALS ['padErrorAction'] == 'pad' )
        padDump ( $error );

    } catch (Throwable $e) {

      padErrorStop ( 'ERROR-CATCH: ' . $e->getMessage(), $e->getFile(), $e->getLine(), $error );

    }

    error_reporting ($reporting);
    restore_error_handler ();

    $GLOBALS ['padExit'] = 1;
      
    return FALSE;

  }
  
?>