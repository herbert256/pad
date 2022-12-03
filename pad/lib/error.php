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

    if ( $GLOBALS ['padErrorAction'] == 'php' ) { 
      trigger_error("$file:$line $error", E_USER_ERROR);
      return FALSE;
    }
 
    if ( $GLOBALS ['padErrorAction'] == 'boot' )
      return padBootStop ( $error, $file, $line ); 

    return padErrorGo ( 'PAD: ' . $error, $file, $line ); 
 
  }


  function padErrorError ( $type, $error, $file, $line ) {
 
    if ( error_reporting() & $type ) {

      set_error_handler ( 'padErrorErrorError' );
      padErrorGo ( 'QERROR: ' . $error, $file, $line );
      restore_error_handler ();

      return FALSE;

    }

    $GLOBALS ['padErrorIgnored'] [] = "$file:$line $error";
 
  }


  function padErrorErrorError ( $type, $error, $file, $line ) {

    padErrorDump ("$file:$line WERROR-ERROR: $error");
 
  }


  function padErrorException ( $e ) {

    set_exception_handler ( 'padErrorExceptionException' );
    return padErrorGo ( 'EXCEPTION: ' . $e->getMessage() , $e->getFile(), $e->getLine() );
    restore_exception_handler ();
 
  }

 
  function padErrorExceptionException ( $e ) {

    padErrorDump ( $e->getFile() . ':' . $e->getLine() . ' EXCEPTION-EXCEPTION: ' . $e->getMessage() ); 
 
  }
  

  function padErrorShutdown () {

    if ( isset ( $GLOBALS ['padSkipShutdown'] ) )
      return;

    $error = error_get_last ();

    if ( $error === NULL ) 
      return;
  
    if ( isset ( $GLOBALS ['padDump'] ) )
      return padDumpProblem ( 'SHUTDOWN: ' . $error['message'], $error['file'], $error['line'] );

    return padErrorGo ( 'SHUTDOWN: ' . $error['message'] , $error['file'], $error['line'] );
  
  }


  function padErrorGo ($error, $file, $line) {

    try {
 
      return padErrorTry ($error, $file, $line);

    } catch (Throwable $e) {

      padErrorDump ( $e->getFile() . ':' . $e->getLine() . ' ERROR-CATCH: ' . $e->getMessage() );

    }

  }


  function padErrorTry ($error, $file, $line) {

    if ( $GLOBALS ['padErrorAction'] == 'ignore' ) 
      return FALSE;

    if ( $GLOBALS['padExit'] <> 1 )
      padErrorDump ("$file:$line ERROR-SECOND: $error");
    else
      $GLOBALS['padExit'] = 2;

    $error = "$file:$line " . padMakeSafe ( $error );

    $GLOBALS ['padErrrorList'] [] = $error;

    if ( $GLOBALS ['padErrorLog'] or in_array ( $GLOBALS ['padErrorAction'], ['report', 'pad'] ) )
      padErrorLog ( $error );

    if ( $GLOBALS ['padTrace'] )
      padErrorTrace ( $error ); 

    if ( $GLOBALS ['padErrorDump'] or $GLOBALS ['padErrorAction'] == 'report' )
      padErrorReport ( $error );

    if ( ! headers_sent () and in_array ( $GLOBALS ['padErrorAction'], ['exit', 'stop', 'pad'] ) )
      padHeader ('HTTP/1.0 500 Internal Server Error' );

    if ( $GLOBALS ['padErrorAction'] == 'exit')

      padExit ();

    elseif ( $GLOBALS ['padErrorAction'] == 'stop' )

      padStop (500);

    elseif ( $GLOBALS ['padErrorAction'] == 'pad' )

      padDump ( $error );

    elseif ( $GLOBALS ['padErrorAction'] == 'report' )

      $GLOBALS ['padExit'] = 1;

    else

      padBootError ( "ERROR-LOGIC: $error" );
    
    return FALSE;

  }


  function padErrorLog ( $info ) {

    $id = $GLOBALS ['PADREQID'] ?? uniqid (TRUE);
  
    if ( is_array($info) or is_object($info) )
      $info = padJson ($info);

    if ( ! $info ) {

      $padDebugBacktrace = debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS);

      foreach ( $padDebugBacktrace as $key => $trace ) {
        extract ( $trace );
        $info .= "$file:$line:$function ";
      }

    }

    $info = padMakeSafe ( $info );

    error_log ( "[PAD] $id $info", 4 );

  }


  function padErrorID () {

    $id = $GLOBALS ['PADREQID'] ?? uniqid (TRUE);

    echo "Error: $id";

  }


  function padErrorReport ($error) {

    global $padErrCnt;

    $id = $GLOBALS ['PADREQID'] ?? uniqid (TRUE);

    $padErrCnt++;
    
    padFilePutContents ( "errors/$id-$padErrCnt.html", padDumpGet($error) );

  }


  function padErrorTrace ( $error ) {

    global $pad, $padOccurDir;

    $padErrorDir = $padOccurDir [$pad] . "/error";

    padFilePutContents ( "$padErrorDir/error.html", padDumpGet($error) ); 
    padTraceAll        ( $padErrorDir );

  }


 function padErrorDump ( $error ) {

    gc_collect_cycles ();
 
    $GLOBALS ['padErrrorList'] [] = $error;
 
    padDump ($error);

  }

 
?>