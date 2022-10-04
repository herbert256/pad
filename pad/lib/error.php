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
 
    return padErrorGo ( 'PAD: ' . $error, $file, $line); 
 
  }


  function padErrorHandler ( $type, $error, $file, $line ) {
 
    if ( error_reporting() & $type )
      return padErrorGo ( 'ERROR: ' . $error, $file, $line );
 
  }


  function padErrorException ( $error ) {

    return padErrorGo ( 'EXCEPTION: ' . $error->getMessage() , $error->getFile(), $error->getLine() );
 
  }
  

  function padErrorShutdown () {

    if ( isset ( $GLOBALS ['padSkipShutdown'] ) )
      return;

    $error = error_get_last ();

    if ( $error === NULL ) 
      return;
  
    return padErrorGo ( 'SHUTDOWN: ' . $error['message'] , $error['file'], $error['line'] );
  
  }


  function padErrorGo ($error, $file, $line) {

    try {
 
      return padErrorTry ($error, $file, $line); 
 
    } catch (Exception $e) {

      return padBootGo ( $e->getMessage(),$e->getFile(), $e->getLine() );
 
    }

  }


  function padErrorTry ($error, $file, $line) {

    $GLOBALS ['padErrrorList'] [] = "$file:$line $error";

    if ( $GLOBALS ['padErrorAction'] == 'ignore' ) 
      return FALSE;

    if ( $GLOBALS['padExit'] <> 1 )
      return padBootGo ($error, $file, $line);
    else
      $GLOBALS['padExit'] = 2;

    global $padErrorAction, $padExit, $PADREQID, $padTrace, $padErrorDump, $padErrorLog, $padErrCnt;

    $padErrCnt++;

    if ( $padErrorDump or $padErrorAction == 'report' )
      padFilePutContents ( "errors/$PADREQID-$padErrCnt.html", padDumpGet($error) );

    padErrorTrace ( $error );

    $error = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '.', $error);
    $error = preg_replace('/\s+/', ' ', $error);
    if ( strlen($error) > 255 )
      $error = substr($error, 0, 255);
    $error = trim($error);
    $error = "$file:$line $error";

    if ( $padErrorLog and $padErrorAction <> 'boot' ) 
      error_log ("[PAD] $PADREQID $error", 4);   

    if ( ! headers_sent () and in_array($padErrorAction, ['pad', 'stop', 'abort']) )
      padHeader ('HTTP/1.0 500 Internal Server Error' );

    if ( $padErrorAction == 'boot' )

      padBootGo ( $error, $file, $line );

    elseif ( $padErrorAction == 'abort')

      padExit ();

    elseif ( $padErrorAction == 'report' ) {

      $padExit = 1;
      return FALSE;

    } elseif ( $padErrorAction == 'pad' ) {

      if ( padLocal() )
        padDump ("Error: $PADREQID:  $error");
      else
        echo "Error: $PADREQID";

      $GLOBALS ['padSent'] = TRUE;             

    }

    padStop (500);

  }


  function padErrorTrace ( $error ) {

    if ( ! $GLOBALS ['padTrace'] )
      return;

    global $pad, $padOccurDir;

    $padErrorDir = $padOccurDir [$pad] . "/error";

    padFilePutContents ( "$padErrorDir/err.html", padDumpGet($error) ); 
    padTraceAll        ( $padErrorDir );

  }


?>