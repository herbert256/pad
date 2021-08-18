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
    $error   = (int)            E_ERROR | E_USER_ERROR | E_CORE_ERROR | E_COMPILE_ERROR | E_PARSE;
    $warning = (int) $error   | E_RECOVERABLE_ERROR | E_WARNING | E_USER_WARNING | E_CORE_WARNING | E_COMPILE_WARNING;
    $notice  = (int) $warning | E_NOTICE | E_USER_NOTICE;
    $all     = (int) $notice  | E_DEPRECATED | E_USER_DEPRECATED | E_STRICT;

    error_reporting ( $$level );
    
  }


  function pad_error_handler ( $type, $error, $file, $line ) {
 
    if ( error_reporting() & $type )
      return pad_error ( 'ERROR: ' . $error, $file, $line );
 
  }


  function pad_error_exception ( $error ) {

    return pad_error ( 'EXCEPTION: ' . $error->getMessage() , $error->getFile(), $error->getLine() );
 
  }
  

  function pad_error_shutdown () {

    if ( isset ( $GLOBALS['pad_skip_shutdown'] ) )
      return;

    $error = error_get_last ();

    if ( $error === NULL ) 
      return;
  
    return pad_error ( 'SHUTDOWN: ' . $error['message'] , $error['file'], $error['line'] );
  
  }


  function pad_error ($error, $file='', $line='') {

    if ( $GLOBALS['pad_exit'] <> 1 ) 
      pad_boot_error ( "ERROR-2: " . $error, $file, $line );

    if ( ! $file )
      try {
        extract ( debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS, 1) [0] );
      } catch (Exception $e) {
        pad_boot_error ( "ERROR-3: " . $e->getMessage(), $e->getFile(), $e->getLine() );
      }

    if ( $pad_error_action == 'php' )
      throw new ErrorException ($error, 0, E_ERROR, $file, $line);

    try {
      return pad_error_go ($error, $file, $line); 
    } catch (Exception $e) {
      pad_boot_error ( "ERROR-4: " . $e->getMessage(), $e->getFile(), $e->getLine() );
    }

  }


  function pad_error_go ($error, $file, $line) {

    global $app, $page, $pad_error_action, $pad_exit, $pad_trace, $pad_trace_dir_base, $PADREQID;

    $pad_exit = 2;

    $error = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '.', $error);
    if ( strlen($error) > 255 )
      $error = substr($error, 0, 255);

    if ( $pad_error_action <> 'boot' ) 
      error_log ("[PAD] $PADREQID $file:$line $error", 4);   

    if ( $pad_trace )
      pad_file_put_contents (
        $pad_trace_dir_base . "/errors/" . uniqid() . ".html",
        pad_dump_get ("ERROR: $file:$line $error")  
      );

    if ( ! headers_sent () and in_array($pad_error_action, ['pad', 'stop', 'abort']) )
      pad_header ('HTTP/1.0 500 Internal Server Error' );

    if ( $GLOBALS['pad_error_dump'] or $pad_error_action == 'report' or $pad_error_action == 'pad' ) 
      pad_dump_to_file ("errors/$app/$page/$PADREQID." .uniqid() . ".html", "$file:$line $error");

    if ( $pad_error_action == 'boot' )

      pad_boot_error ( $error, $file, $line );

    elseif ( $pad_error_action == 'abort')

      pad_exit ();

    elseif ( $pad_error_action == 'none' or $pad_error_action == 'report' ) {

      $pad_exit = 1;
      return FALSE;

    } elseif ( $pad_error_action == 'pad' ) {

      if ( pad_local() )
        pad_dump ("Error: $PADREQID: $file:$line $error");
      else
        echo "Error: $PADREQID";

      $GLOBALS ['pad_sent'] = TRUE;             

    }

    pad_stop (500);

  }
  

?>