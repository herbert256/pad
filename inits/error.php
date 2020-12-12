<?php
 
 
  if ( $pad_error_action == 'boot' ) 
    return;

  restore_error_handler ();
  restore_exception_handler ();
  $pad_no_boot_shutdown = TRUE;

  if ( $pad_error_action == 'php' ) {
    ini_set ('display_errors', $pad_display_errors);
    error_reporting ( $pad_error_reporting );
    return;
  }

  set_error_handler          ( 'pad_error_handler'   );
  set_exception_handler      ( 'pad_error_exception' );
  register_shutdown_function ( 'pad_error_shutdown'  );

  pad_error_reporting ( $pad_error_level );
  
 
  function pad_error_reporting ($level) {

    $none    = (int) 0;
    $error   = (int)            E_ERROR | E_USER_ERROR | E_CORE_ERROR | E_COMPILE_ERROR | E_PARSE;
    $warning = (int) $error   | E_RECOVERABLE_ERROR | E_WARNING | E_USER_WARNING | E_CORE_WARNING | E_COMPILE_WARNING;
    $notice  = (int) $warning | E_NOTICE | E_USER_NOTICE;
    $all     = (int) $notice  | E_DEPRECATED | E_USER_DEPRECATED | E_STRICT;

    error_reporting ( $$level );
    
  }
  

  function pad_error ( $error ) {

    extract ( debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS, 1) [0] );

    if ( $GLOBALS['pad_error_action'] == 'boot' ) 
      pad_boot_error_go ($error, $file, $line);

    if ( $GLOBALS['pad_error_action'] == 'php' )
      throw new ErrorException ($error, 0, E_ERROR, $file, $line);

    return pad_error_go ($error, $file, $line);
 
  }


  function pad_error_handler ( $type, $error, $file, $line ) {
 
    if ( error_reporting() & $type )
      return pad_error_go ($error, $file, $line);

  }


  function pad_error_exception ( $error ) {

    return pad_error_go ( $error->getMessage(), $error->getFile(), $error->getLine()) ;

  }
  

  function pad_error_shutdown () {

    if ( $GLOBALS['pad_exit'] == 9 )
      exit;

    $error = error_get_last ();
    if ($error !== NULL)
      return pad_error_go ( $error['message']??'', $error['file']??'', $error['line']??'');
    
  }


  function pad_error_go ($error, $file, $line) {

    global $pad_error_action, $PADREQID;    

    $file = str_replace (PAD_APP,  'APP://', $file);
    $file = str_replace (PAD_HOME, 'PAD://', $file);
    $msg  = "$file:$line $error";
    error_log ("[PAD] $PADREQID $msg", 4);   

    if ( $GLOBALS['pad_exit'] == 2 )
      pad_boot_error_go ($error, $file, $line);
    $GLOBALS['pad_exit'] = 2;
    
    if ( $pad_error_action == 'abort') 
      pad_exit ();

    if ( $pad_error_action == 'none') 
      return FALSE;    

    if ( $pad_error_action == 'report') {
      error_log ("[PAD] $PADREQID $file/$line $error", 4);
      return FALSE;
    }
          
    restore_exception_handler ();
    restore_error_handler     ();

    set_error_handler     ( 'pad_boot_error_handler'     );
    set_exception_handler ( 'pad_boot_exception_handler' );
    unset ($GLOBALS['pad_no_boot_shutdown']);

    $buffer  = '';
    $buffers = ob_get_level ();
    for ($i = 1; $i <= $buffers; $i++)
      $buffer .= ob_get_clean();
   
    if ( $GLOBALS['pad_track_errors'] ) 
      pad_track_vars ("errors/$PADREQID.html", $msg);
  
    if ( ! headers_sent() )
      header ( 'HTTP/1.0 500 Internal Server Error' );

    if ( pad_local() ) {
      pad_close_html();
      echo "<pre><hr><b>$msg</b><hr></pre>";
    } else {
      echo "Error: $PADREQID";
    }

    $GLOBALS ['pad_sent'] = TRUE;

    pad_dump ();

    $GLOBALS ['pad_stop'] = 500;
    include PAD_HOME . 'exits/stop.php';

  }

?>