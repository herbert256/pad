<?php
  

  function padErrorReporting ( $level ) {

    $none    = (int) 0;
    $error   = (int) $none    | E_ERROR | E_USER_ERROR | E_CORE_ERROR | E_COMPILE_ERROR | E_PARSE;
    $warning = (int) $error   | E_RECOVERABLE_ERROR | E_WARNING | E_USER_WARNING | E_CORE_WARNING | E_COMPILE_WARNING;
    $notice  = (int) $warning | E_NOTICE | E_USER_NOTICE;
    $all     = (int) $notice  | E_DEPRECATED | E_USER_DEPRECATED | E_STRICT;

    error_reporting ( $$level );
    
  }


  function padError ($error) {
 
    extract ( debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS, 1) [0] );

    $file     = $file     ?? '???';
    $line     = $line     ?? '???';
    $function = $function ?? '???';

    if ( $GLOBALS ['padErrorAction'] == 'php' ) { 
      trigger_error ("$file:$line $error", E_USER_ERROR);
      return FALSE;
    }
 
    if ( $GLOBALS ['padErrorAction'] == 'boot' )
      return padBootStop ( $error, $file, $line ); 

    return padErrorGo ( 'PAD: ' . $error, $file, $line ); 
 
  }


  function padErrorHandler ( $type, $error, $file, $line ) {
 
    if ( error_reporting() & $type )
      return padErrorGo ( 'ERROR: ' . $error, $file, $line );

    $GLOBALS ['padErrorIgnored'] [] = "$file:$line $error";
 
  }


  function padErrorException ( $error ) {

    $GLOBALS ['padErrorException'] = $error;

    return padErrorGo ( 'EXCEPTION: ' . $error->getMessage() , $error->getFile(), $error->getLine() );
     
  }


  function padErrorShutdown () {

    if ( isset ( $GLOBALS ['padSkipShutdown'] ) )
      return;

    $error = error_get_last ();

    if ( $error !== NULL ) 
      return padErrorGo ( 'SHUTDOWN: ' . $error['message'] , $error['file'], $error['line'] );
  
  }


  function padErrorGo ($error, $file, $line) {

    if ( $GLOBALS ['padErrorAction'] == 'exit') {
      padHeader ('HTTP/1.0 500 Internal Server Error' );
      padExit ();
    }

    if ( $GLOBALS['padExit'] <> 1 )
      padErrorStop ( "ERROR-SECOND: $error", $file, $line);
    
    $GLOBALS['padExit'] = 2;

    $GLOBALS['padErrorError'] = $error;
    $GLOBALS['padErrorFile']  = $file;
    $GLOBALS['padErrorLine']  = $line;

    $error = "$file:$line " . padMakeSafe ( $error );

    $GLOBALS ['padErrrorList'] [] = $error; 

    try {
 
      if ( $GLOBALS ['padErrorLog'] or $GLOBALS ['padErrorAction'] == 'report' )
        padErrorLog ( $error );

      if ( $GLOBALS ['padErrorReport'] or $GLOBALS ['padErrorAction'] == 'report' )
        padDumpToDir ( $error );

      if ( $GLOBALS ['padErrorAction'] == 'stop' )
        padStop ( 500 );

      if ( $GLOBALS ['padErrorAction'] == 'pad' )
        padDump ( $error );

    } catch (Throwable $e) {

      padErrorStop ( 'ERROR-CATCH: ' . $e->getMessage(), $e->getFile(), $e->getLine() );

    }

    $GLOBALS ['padExit'] = 1;
      
    return FALSE;

  }


  function padErrorLog ( $info ) {
  
    if ( is_array($info) or is_object($info) )
      $info = padJson ($info);

    error_log ( '[PAD] ' . padID () . padMakeSafe ( $info ), 4 );

  }


  function padErrorStop ( $error, $file, $line) {

    if ( isset ( $GLOBALS ['padErrrorList'] ) ) {

      $GLOBALS ['padErrrorList'] = array_unique ( $GLOBALS ['padErrrorList'] );

      foreach ( $GLOBALS ['padErrrorList'] as $list )
        $error .= "\n" . $list;

    }

    padBootStop ( $error, $file, $line );

  }

 
?>