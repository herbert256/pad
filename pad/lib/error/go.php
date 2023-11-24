<?php


  function padErrorGo ($error, $file, $line) {

    if ( $GLOBALS ['padErrorAction'] == 'ignore' ) 
      return FALSE;

    if ( $GLOBALS['padExit'] <> 1 )
      padErrorStop ( "ERROR-SECOND: $error", $file, $line);

    $GLOBALS['padExit'] = 2;
    
    $GLOBALS['padErrorFile']  = $file;
    $GLOBALS['padErrorLine']  = $line;
    $GLOBALS['padErrorError'] = $error;

    $error = "$file:$line " . padMakeSafe ( $error );

    set_error_handler ( 'padErrorThrow' );

    try {

      padErrorGoGo ( $error );
 
    } catch (Throwable $e) {

      padErrorGoCatch ( $e, $error );

    }

    restore_error_handler ();

    $GLOBALS ['padExit'] = 1;
      
    return FALSE;

  }


  function padErrorGoGo ( $error ) {

    if ( $GLOBALS ['padTraceActive'] )
      include pad . 'tail/types/trace/error/error.php';    

    $GLOBALS ['padErrrorList'] [] = $error; 

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

  }


  function padErrorGoCatch ( $e, $error ) {
    
    set_error_handler ( 'padErrorThrow' );

    try {

      padErrorStop ( 'ERROR-CATCH: ' . $e->getMessage(), $e->getFile(), $e->getLine(), $error );
 
    } catch (Throwable $e2) {

      padErrorGoCatchCatch ( $e2, $e, $error );

    }

    restore_error_handler ();

  }


  function padErrorGoCatchCatch ( $e3, $e2, $error1 ) {
    
    set_error_handler ( 'padErrorThrow' );

    try {

      $error2 = $e2->getFile() . ':' . $e2->getLine() . ' ' . $e2->getMessage();
      $error3 = $e3->getFile() . ':' . $e3->getLine() . ' ' . $e3->getMessage();

      padErrorLog ( $error1 );
      padErrorLog ( $error2 );
      padErrorLog ( $error3 );

      padErrorExit ( "$error1\n$error2\n$error3" );

    } catch (Throwable $ignored) {

      echo 'oops';

      $GLOBALS ['padSkipShutdown']     = TRUE;
      $GLOBALS ['padSkipBootShutdown'] = TRUE;
      
      exit;

    }

  }

  
?>