<?php
  

  function padErrorShort ( $error ) {

    $GLOBALS ['padSkip_boot_shutdown'] = TRUE;
    $GLOBALS ['padSkip_shutdown']      = TRUE;

    echo ( "<pre><b>$error</b>\n\n");
    
    $padDebug_backtrace = debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS);
    
    foreach ( $padDebug_backtrace as $key => $trace ) {
      extract ( $trace );
      echo ( "$file:$line - $function\n");
    }
    
    echo "\n" . htmlentities ( print_r ( $GLOBALS, TRUE ) );
    
    exit;
 
  }


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

    if ( $GLOBALS ['padError_action'] == 'php' ) { 
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

    if ( isset ( $GLOBALS ['padSkip_shutdown'] ) )
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

      return padErrorError ( $e->getMessage(),$e->getFile(), $e->getLine() );
 
    }

  }


  function padErrorTry ($error, $file, $line) {

    if ( $GLOBALS ['padError_action'] == 'ignore' ) 
      return FALSE;

    global $padError_action, $padExit, $PADREQID, $padTrace, $padError_dump, $padError_log, $padErrCnt;

    if ( $GLOBALS ['padExit'] <> 1 )
      return padErrorError ($error, $file, $line);

    $GLOBALS ['padExit'] = 2;

    $padErrCnt++;

    $error = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '.', $error);
    $error = preg_replace('/\s+/', ' ', $error);
    if ( strlen($error) > 255 )
      $error = substr($error, 0, 255);
    $error = trim($error);
    $error = "$file:$line $error";

    $GLOBALS ['padErrror_list'] [] = $error;

    if ( $padError_log and $padError_action <> 'boot' ) 
      error_log ("[PAD] $PADREQID $error", 4);   

    if ( $padTrace or $padError_dump or $padError_action == 'report' )
      padTraceWriteError ( $error, 'error', $padErrCnt, [], 1);

    if ( ! headers_sent () and in_array($padError_action, ['pad', 'stop', 'abort', 'ignore']) )
      padHeader ('HTTP/1.0 500 Internal Server Error' );

    if ( $padError_action == 'boot' )

      padBootError ( $error, $file, $line );

    elseif ( $padError_action == 'abort')

      padExit ();

    elseif ( $padError_action == 'report' ) {

      $padExit = 1;
      return FALSE;

    } elseif ( $padError_action == 'pad' ) {

      if ( padLocal() )
        padDump ("Error: $PADREQID:  $error");
      else
        echo "Error: $PADREQID";

      $GLOBALS ['padSent'] = TRUE;             

    }

    padStop (500);

  }
 

  function padErrorError ($error, $file, $line) {

    try {
 
      global $PADREQID;

      $error = "[PAD] $PADREQID Error-in-error: $file:$line $error";

      error_log ($error, 4);

      padFilePutContents ( "errors/$PADREQID.html", padDumpGet($error) );

      if ( padLocal() )
        padDump ($error);
      else
        echo "Error: $PADREQID";

      $GLOBALS ['padSkip_shutdown']      = TRUE;
      $GLOBALS ['padSkip_boot_shutdown'] = TRUE;
      
      exit;
 
    } catch (Exception $e) {

      padBootError ( $error, $file, $line );
 
    }     


  }


?>