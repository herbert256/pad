<?php

  if ( $pad_error_action == 'php' )
    return;

  ini_set ('display_errors', 0);
  
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

    if ( ($GLOBALS['pad_error_action']??'') == 'php' ) {
      throw new ErrorException ($error, 0, E_ERROR, $file, $line);
      return FALSE;
    }

    return pad_error_go ($error, $file, $line);
 
  }




  function pad_error_handler ( $type, $error, $file, $line ) {

    $GLOBALS ['pad_errors'] [] = pad_error_msg ($error, $file, $line);
 
    if ( error_reporting() & $type )
      pad_error_go ($error, $file, $line);
      
    return TRUE;

  }


  function pad_error_exception ( $error ) {

    return pad_error_go ( 'EXCEPTION: ' . $error->getMessage(), $error->getFile(), $error->getLine()) ;

  }
  

  function pad_error_shutdown () {

    if ( $GLOBALS['pad_exit'] == 9 )
      exit;

    if ( isset($GLOBALS['pad_html']) )       unset($GLOBALS['pad_html']);
    if ( isset($GLOBALS['pad_result']) )     unset($GLOBALS['pad_result']);
    if ( isset($GLOBALS['pad_parameters']) ) unset($GLOBALS['pad_parameters']);

    gc_collect_cycles();

    $error = error_get_last ();

    if ( ! is_null($error) )
      pad_error_go ( 'SHUTDOWN: ' . $error['message'], $error['file'], $error['line'] );
    
  }


  function pad_error_go ($error, $file, $line) {

    global $pad_error_action, $PADREQID;

    set_error_handler     ('pad_error_handler_2'  );
    set_exception_handler ('pad_error_exception_2');
        
    if ( ! in_array($pad_error_action, ['report', 'ignore']) ) {    
      $buffer  = '';
      $buffers = ob_get_level ();
      for ($i = 1; $i <= $buffers; $i++)
        $buffer .= ob_get_clean();
    }
 
    if ( ($GLOBALS['pad_exit']??0) <> 1 )
      pad_error_2 ("TWO: $error", $file, $line);
  
    $GLOBALS['pad_exit'] = 2;
      
    $msg = pad_error_msg ($error, $file, $line);

    if ( $GLOBALS['pad_track_errors'] or $pad_error_action == 'report' ) 
      pad_track_vars ("errors/$PADREQID.html", $msg);

    if ( $pad_error_action == 'report' ) pad_error_log ( $msg );
    if ( $pad_error_action == 'close'  ) pad_error_stop ();
    if ( $pad_error_action == 'abort'  ) pad_exit ();
    if ( $pad_error_action == 'pad'    ) pad_error_pad ($msg);

    restore_error_handler     ();
    restore_exception_handler ();

    $GLOBALS['pad_exit'] = 1;

    return FALSE;

  }
  

  function pad_error_pad ( $error ) {

    for ($i = 1; $i <= ob_get_level (); $i++)
      ob_end_clean();

    pad_error_show ( $error );

    pad_dump ();

    pad_error_stop ();

  }


  function pad_error_handler_2 ($type, $error, $file, $line) {

    pad_error_2 ( 'ERROR-2: ' . $error, $file, $line );

  }
  
  
  function pad_error_exception_2 ($error) {

    pad_error_2 ( 'EXCEPTION-2: ' . $error->getMessage(), $error->getFile(), $error->getLine()) ;

  }


  function pad_error_2 ( $error, $file, $line ) {

    $GLOBALS ['pad_exit'] = 9;

    pad_error_show ( pad_error_msg ($error, $file, $line ) );

    pad_exit ();

  }

  
  function pad_error_show ( $error ) {

    if ( ! headers_sent() )
      header ( 'HTTP/1.0 500 Internal Server Error' );

    if ( pad_local() )
      echo "<pre><hr><b>$error</b><hr></pre>";
    else {
      echo 'Ërror: ' . pad_id ();
      pad_error_log ( $error );
    }

    $GLOBALS ['pad_sent'] = TRUE;
    
  }


  function pad_error_stop () {

    $GLOBALS ['pad_stop'] = 500;

    include PAD_HOME . 'exits/stop.php';

  }

  
  function pad_error_msg ($error, $file, $line) {

    global $app, $page;

    $file = str_replace ( PAD_APP,   'APP:', $file);
    $file = str_replace ( PAD_HOME,  'PAD:', $file);

    return "$file:$line $error";
    
  }


  function pad_error_log ( $error ) {

    error_log ("[PAD] - " . pad_id () . " - $error", 4);

  }

  
?>