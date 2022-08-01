<?php
  
  if ( $pad_error_action == 'boot' ) 
    return;

  restore_error_handler ();
  restore_exception_handler ();
  $pad_skip_boot_shutdown = TRUE;

  if ( $pad_error_action == 'php' ) {
    ini_set ('display_errors', $pad_display_errors);
    error_reporting ( $pad_error_reporting );
    return;
  }

  set_error_handler          ( 'pad_error_handler'   );
  set_exception_handler      ( 'pad_error_exception' );
  register_shutdown_function ( 'pad_error_shutdown'  );

  pad_error_reporting ( $pad_error_level );

  
  function pad_error_reporting ( $level ) {

    $none    = (int) 0;
    $error   = (int) $none    | E_ERROR | E_USER_ERROR | E_CORE_ERROR | E_COMPILE_ERROR | E_PARSE;
    $warning = (int) $error   | E_RECOVERABLE_ERROR | E_WARNING | E_USER_WARNING | E_CORE_WARNING | E_COMPILE_WARNING;
    $notice  = (int) $warning | E_NOTICE | E_USER_NOTICE;
    $all     = (int) $notice  | E_DEPRECATED | E_USER_DEPRECATED | E_STRICT;

    error_reporting ( $$level );
    
  }


  function pad_error ($error) {
 
    extract ( debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS, 1) [0] );

    if ( $GLOBALS['pad_error_action'] == 'php' ) { 
      trigger_error("$file:$line $error", E_USER_ERROR);
      return FALSE;
    }
 
    return pad_error_go ( 'PAD: ' . $error, $file, $line); 
 
  }


  function pad_error_handler ( $type, $error, $file, $line ) {
 
    if ( error_reporting() & $type )
      return pad_error_go ( 'ERROR: ' . $error, $file, $line );
 
  }


  function pad_error_exception ( $error ) {

    return pad_error_go ( 'EXCEPTION: ' . $error->getMessage() , $error->getFile(), $error->getLine() );
 
  }
  

  function pad_error_shutdown () {

    if ( isset ( $GLOBALS['pad_skip_shutdown'] ) )
      return;

    $error = error_get_last ();

    if ( $error === NULL ) 
      return;
  
    return pad_error_go ( 'SHUTDOWN: ' . $error['message'] , $error['file'], $error['line'] );
  
  }


  function pad_error_go ($error, $file, $line) {

    try {
 
      return pad_error_try ($error, $file, $line); 
 
    } catch (Exception $e) {

      return pad_error_error ( $e->getMessage(),$e->getFile(), $e->getLine() );
 
    }

  }


  function pad_error_try ($error, $file, $line) {

    if ( $GLOBALS['pad_error_action'] == 'ignore' ) 
      return FALSE;

    global $pad_error_action, $pad_exit, $PADREQID, $pad_trace_errors, $pad_error_dump, $pad_error_log, $pad_err_cnt;

    if ( $GLOBALS['pad_exit'] <> 1 )
      return pad_error_error ($error, $file, $line);

    $GLOBALS['pad_exit'] = 2;

    $pad_err_cnt++;

    $error = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '.', $error);
    $error = preg_replace('/\s+/', ' ', $error);
    if ( strlen($error) > 255 )
      $error = substr($error, 0, 255);
    $error = trim($error);
    $error = "$file:$line $error";

    if ( $pad_error_log and $pad_error_action <> 'boot' ) 
      error_log ("[PAD] $PADREQID $error", 4);   

    if ( $pad_trace_errors or $pad_error_dump or $pad_error_action == 'report' )
      pad_trace_write_error ( $error, 'error', $pad_err_cnt, [], 1);

    if ( ! headers_sent () and in_array($pad_error_action, ['pad', 'stop', 'abort', 'ignore']) )
      pad_header ('HTTP/1.0 500 Internal Server Error' );

    if ( $pad_error_action == 'boot' )

      pad_boot_error ( $error, $file, $line );

    elseif ( $pad_error_action == 'abort')

      pad_exit ();

    elseif ( $pad_error_action == 'report' ) {

      $pad_exit = 1;
      return FALSE;

    } elseif ( $pad_error_action == 'pad' ) {

      if ( pad_local() )
        pad_dump ("Error: $PADREQID:  $error");
      else
        echo "Error: $PADREQID";

      $GLOBALS ['pad_sent'] = TRUE;             

    }

    pad_stop (500);

  }
 

  function pad_error_error ($error, $file, $line) {

    try {
 
      global $PADREQID;

      $error = "[PAD] $PADREQID Error-in-error: $file:$line $error";

      error_log ($error, 4);

      pad_file_put_contents ( "errors/$PADREQID.html", pad_dump_get($error) );

      if ( pad_local() )
        pad_dump ($error);
      else
        echo "Error: $PADREQID";

      $GLOBALS['pad_skip_shutdown']      = TRUE;
      $GLOBALS['pad_skip_boot_shutdown'] = TRUE;
      
      exit;
 
    } catch (Exception $e) {

      pad_boot_error ( $error, $file, $line );
 
    }     


  }


?>