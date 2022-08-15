<?php
  

  function pErrorShort ( $error ) {

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


  function pError_reporting ( $level ) {

    $none    = (int) 0;
    $error   = (int) $none    | E_ERROR | E_USER_ERROR | E_CORE_ERROR | E_COMPILE_ERROR | E_PARSE;
    $warning = (int) $error   | E_RECOVERABLE_ERROR | E_WARNING | E_USER_WARNING | E_CORE_WARNING | E_COMPILE_WARNING;
    $notice  = (int) $warning | E_NOTICE | E_USER_NOTICE;
    $all     = (int) $notice  | E_DEPRECATED | E_USER_DEPRECATED | E_STRICT;

    error_reporting ( $$level );
    
  }


  function pError ($error) {
 
    extract ( debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS, 1) [0] );

    if ( $GLOBALS ['padError_action'] == 'php' ) { 
      trigger_error("$file:$line $error", E_USER_ERROR);
      return FALSE;
    }
 
    return pError_go ( 'PAD: ' . $error, $file, $line); 
 
  }


  function pError_handler ( $type, $error, $file, $line ) {
 
    if ( error_reporting() & $type )
      return pError_go ( 'ERROR: ' . $error, $file, $line );
 
  }


  function pError_exception ( $error ) {

    return pError_go ( 'EXCEPTION: ' . $error->getMessage() , $error->getFile(), $error->getLine() );
 
  }
  

  function pError_shutdown () {

    if ( isset ( $GLOBALS ['padSkip_shutdown'] ) )
      return;

    $error = error_get_last ();

    if ( $error === NULL ) 
      return;
  
    return pError_go ( 'SHUTDOWN: ' . $error['message'] , $error['file'], $error['line'] );
  
  }


  function pError_go ($error, $file, $line) {

    try {
 
      return pError_try ($error, $file, $line); 
 
    } catch (Exception $e) {

      return pError_error ( $e->getMessage(),$e->getFile(), $e->getLine() );
 
    }

  }


  function pError_try ($error, $file, $line) {

    if ( $GLOBALS ['padError_action'] == 'ignore' ) 
      return FALSE;

    global $padError_action, $padExit, $PADREQID, $padTrace, $padError_dump, $padError_log, $padErrCnt;

    if ( $GLOBALS ['padExit'] <> 1 )
      return pError_error ($error, $file, $line);

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
      pTrace_write_error ( $error, 'error', $padErrCnt, [], 1);

    if ( ! headers_sent () and in_array($padError_action, ['pad', 'stop', 'abort', 'ignore']) )
      pHeader ('HTTP/1.0 500 Internal Server Error' );

    if ( $padError_action == 'boot' )

      pBoot_error ( $error, $file, $line );

    elseif ( $padError_action == 'abort')

      pExit ();

    elseif ( $padError_action == 'report' ) {

      $padExit = 1;
      return FALSE;

    } elseif ( $padError_action == 'pad' ) {

      if ( pLocal() )
        pDump ("Error: $PADREQID:  $error");
      else
        echo "Error: $PADREQID";

      $GLOBALS ['padSent'] = TRUE;             

    }

    pStop (500);

  }
 

  function pError_error ($error, $file, $line) {

    try {
 
      global $PADREQID;

      $error = "[PAD] $PADREQID Error-in-error: $file:$line $error";

      error_log ($error, 4);

      pFile_put_contents ( "errors/$PADREQID.html", pDump_get($error) );

      if ( pLocal() )
        pDump ($error);
      else
        echo "Error: $PADREQID";

      $GLOBALS ['padSkip_shutdown']      = TRUE;
      $GLOBALS ['padSkip_boot_shutdown'] = TRUE;
      
      exit;
 
    } catch (Exception $e) {

      pBoot_error ( $error, $file, $line );
 
    }     


  }


?>