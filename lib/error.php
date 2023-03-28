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


  function padErrorHandler ( $type, $error, $file, $line ) {
 
    if ( error_reporting() & $type ) {

      set_error_handler ( 'padErrorHandlerError' );
      padErrorGo ( 'ERROR: ' . $error, $file, $line );
      restore_error_handler ();

      return FALSE;

    }

    $GLOBALS ['padErrorIgnored'] [] = "$file:$line $error";
 
  }


  function padErrorHandlerError ( $type, $error, $file, $line ) {

    padErrorDump ("$file:$line ERROR-ERROR: $error");
 
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

    if ( $error !== NULL ) 
      return padErrorGo ( 'SHUTDOWN: ' . $error['message'] , $error['file'], $error['line'] );
  
  }


  function padErrorGo ($error, $file, $line) {

    if ( $GLOBALS ['padDump'] ?? '' <> '' )
      padDumpProblem ( 'DUMP-SHUTDOWN: ' . $error['message'], $error['file'], $error['line'] );

    try {
 
      return padErrorTry ($error, $file, $line);

    } catch (Throwable $e) {

      padErrorDump ( $e->getFile() . ':' . $e->getLine() . ' ERROR-CATCH: ' . $e->getMessage() );

    }

  }


  function padErrorTry ($error, $file, $line) {

    if ( $GLOBALS ['padParse'] ) 
      include PAD . 'parse/error.php';

    if ( $GLOBALS ['padLog'] )
      include PAD . 'log/error.php';

    if ( $GLOBALS ['padErrorAction'] == 'ignore' ) 
      return FALSE;

    if ( $GLOBALS['padExit'] <> 1 )
      padErrorDump ("$file:$line ERROR-SECOND: $error");
    else
      $GLOBALS['padExit'] = 2;

    $error = "$file:$line " . padMakeSafe ( $error );

    $GLOBALS ['padErrrorList'] [] = $error;
    $GLOBALS ['padError']         = $error;

    if ( $GLOBALS ['padErrorLog'] or in_array ( $GLOBALS ['padErrorAction'], ['report', 'pad'] ) )
      padErrorLog ( $error );

    if ( $GLOBALS ['padTrace'] )
      padErrorTrace ( $error ); 

    if ( $GLOBALS ['padErrorReport'] or $GLOBALS ['padErrorAction'] == 'report' )
      padErrorReport ( $error ); 

    if ( ! headers_sent () and in_array ( $GLOBALS ['padErrorAction'], ['exit', 'stop', 'pad'] ) )
      padHeader ('HTTP/1.0 500 Internal Server Error' );

    if ( $GLOBALS ['padErrorAction'] == 'exit')

      padExit ();

    elseif ( $GLOBALS ['padErrorAction'] == 'stop' )

      padStop (500);

    elseif ( $GLOBALS ['padErrorAction'] == 'pad' ) {

      $GLOBALS ['padError'] = '';
      padDump ( $error );

    } elseif ( $GLOBALS ['padErrorAction'] == 'report' )

      $GLOBALS ['padExit'] = 1;
    
    return FALSE;

  }


  function padErrorLog ( $info ) {

    $id = padID ();
  
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

    $id = padID ();

    echo "Error: $id";

  }


  function padErrorTrace ( $error ) {

    global $padTraceDir, $padLevelDir, $padOccurDir, $pad, $padTrcCnt, $padInOccur;

    $padTrcCnt++;

    $id = padID ();

    if ( $pad > 1 and $padInOccur and isset ($padOccurDir [$pad]) )
      $dir = $padOccurDir [$pad];
    elseif ( $pad > 1 and isset ($padLevelDir [$pad]) )
      $dir = $padLevelDir [$pad];
    else 
      $dir = $padTraceDir;

    $dir .= "/error-$padTrcCnt";

    padTrace ( $dir, $error );

  }


  function padErrorReport ( $error ) {

    global $padTraceDir, $padLevelDir, $pad, $padErrCnt;

    $padErrCnt++;

    $id = padID ();

    $dir = "errors/$id-"."$padErrCnt";

    padTrace ( $dir, $error );

  }


 function padErrorDump ( $error ) {

    gc_collect_cycles ();
 
    $GLOBALS ['padErrrorList'] [] = $error;
 
    padDump ($error);

  }

 
?>