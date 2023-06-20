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
      trigger_error("$file:$line $error", E_USER_ERROR);
      return FALSE;
    }
 
    if ( $GLOBALS ['padErrorAction'] == 'boot' )
      return padBootStop ( $error, $file, $line ); 

    return padErrorGo ( 'PAD: ' . $error, $file, $line ); 
 
  }


  function padErrorHandler ( $type, $error, $file, $line ) {
 
    if ( error_reporting() & $type ) {

      padErrorGo ( 'ERROR: ' . $error, $file, $line );

      return FALSE;

    }

    $GLOBALS ['padErrorIgnored'] [] = "$file:$line $error";
 
  }


  function padErrorException ( $e ) {

    $GLOBALS ['padExceptions'] [] = $e;

    padErrorGo ( 'EXCEPTION: ' . $e->getMessage() , $e->getFile(), $e->getLine() );
 
    return FALSE;
    
  }


  function padErrorShutdown () {

    if ( isset ( $GLOBALS ['padSkipShutdown'] ) )
      return;

    $error = error_get_last ();

    if ( $error !== NULL ) 
      return padErrorGo ( 'SHUTDOWN: ' . $error['message'] , $error['file'], $error['line'] );
  
  }


  function padErrorGo ($error, $file, $line) {

    if ( $GLOBALS['padExit'] <> 1 )
      padErrorDump ("$file:$line ERROR-SECOND: $error");
    
    $GLOBALS['padExit'] = 2;

    try {

      $GLOBALS ['padErrrorList'] [] = "$file:$line " . padMakeSafe ( $error ); 

      if ( $GLOBALS ['padErrorAction'] == 'ignore' ) 
        return FALSE;

      if ( isset ( $GLOBALS ['padInDump'] ) )
        padDumpProblem ( 'ERROR-DUMP: ' . $error, $file, $line );

      $error = "$file:$line " . padMakeSafe ( $error );

      if ( $GLOBALS ['padErrorLog'] or $GLOBALS ['padErrorAction'] == 'report' )
        padErrorLog ( $error );

      if ( $GLOBALS ['padErrorReport'] or $GLOBALS ['padErrorAction'] == 'report' )
        padDumpToDir ( $error );

      if ( $GLOBALS ['padErrorAction'] == 'exit') {

        if ( ! headers_sent () )
          padHeader ('HTTP/1.0 500 Internal Server Error' );

        padExit ();

      } elseif ( $GLOBALS ['padErrorAction'] == 'stop' )

        padStop (500);

      elseif ( $GLOBALS ['padErrorAction'] == 'pad' ) {

        padDump ( $error );

      } elseif ( $GLOBALS ['padErrorAction'] == 'report' )

        $GLOBALS ['padExit'] = 1;
      
      return FALSE;

    } catch (Throwable $e) {

      $GLOBALS ['padExceptions'] [] = $e;

      padErrorDump ( $e->getFile() . ':' . $e->getLine() . ' ERROR-CATCH: ' . $e->getMessage() );

    }

  }


  function padErrorLog ( $info ) {
  
    if ( is_array($info) or is_object($info) )
      $info = padJson ($info);

    error_log ( '[PAD] ' . padID () . padMakeSafe ( $info ), 4 );

  }


  function padErrorDump ( $error ) {

    $GLOBALS ['padErrrorList'] [] = padMakeSafe ( $error );
 
    padDump ($error);

  }

 
?>