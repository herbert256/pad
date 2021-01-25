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

    if ( $GLOBALS['pad_error_action'] == 'php' )
      throw new ErrorException ($error, 0, E_ERROR, $file, $line);

    return pad_error_go (  $error, $file, $line );
 
  }


  function pad_error_handler ( $type, $error, $file, $line ) {
 
    if ( error_reporting() & $type )
      return pad_error_go ( 'ERROR: ' . $error, $file, $line );

  }


  function pad_error_exception ( $error ) {

    return pad_error_go (  'EXCEPTION: ' . $error->getMessage() , $error->getFile(), $error->getLine()) ;

  }
  

  function pad_error_shutdown () {

    if ( $GLOBALS['pad_exit'] == 9 )
      return;

    $error = error_get_last ();
    if ($error !== NULL)
      return pad_error_go (  'SHUTDOWN: ' . $error['message'] , $error['file'], $error['line'] );
    
  }


  function pad_error_go ($error, $file, $line) {
 
    try {

      $id  = $GLOBALS['PADREQID'] ?? uniqid();
      $msg = "$file:$line $error";

      if ( $GLOBALS['pad_exit'] <> 1 ) 
        return pad_error_error ( $error, $file, $line, $id, '' );

      $GLOBALS['pad_exit'] = 9;

      $msg = str_replace (PAD_APP,  'APP:', $msg);
      $msg = str_replace (PAD_HOME, 'PAD:', $msg);

      pad_trace ('error/error', $msg);   

      if ( $GLOBALS['pad_error_server'] ) 
        error_log ("[PAD] $id $msg", 4);   

      if ( $GLOBALS['pad_track_errors'] ) 
        pad_track_vars ("errors/$id.html", $msg);

      if ( $GLOBALS['pad_error_action'] == 'none') {
        $GLOBALS['pad_exit'] = 1;
        return FALSE;
      }

      $buffers = ob_get_level ();
      for ($i = 1; $i <= $buffers; $i++) {
        $buffer = ob_get_clean();
        pad_trace ('error/buffer', $buffer);
      }

      if ( $GLOBALS['pad_error_action'] == 'boot' ) 
        pad_boot_error_go ($error, $file, $line);


      if ( ! headers_sent() )
         header ( 'HTTP/1.0 500 Internal Server Error' );

      if ( $GLOBALS['pad_error_action'] == 'abort')  {
        echo "Error: $id";
        pad_exit ();
      }

      if ( $GLOBALS['pad_error_action'] <> 'stop') {

        if ( pad_local() )
          echo "<pre><hr><b>$msg</b><hr></pre>";
        else 
          echo "Error: $id";

        $GLOBALS ['pad_sent'] = TRUE;

        pad_dump ();
                     
      }
      
      $GLOBALS ['pad_stop'] = 500;
      include PAD_HOME . 'exits/stop.php';

    } catch (Exception $e) {

      return pad_error_error ( $e->getMessage(), $e->getFile(), $e->getLine() );

    }

  }

  function pad_error_error ($error, $file, $line) {

    try {

      error_log ("[PAD] $file:$line $error", 4);  

      if ( $GLOBALS['pad_error_action'] == 'none') 
        return FALSE;

      echo "Error: $id";

      pad_exit ();

    } catch (Exception $e) {

      if ( $GLOBALS['pad_error_action'] == 'none') 
        return FALSE;

      $GLOBALS['pad_exit'] = 9;
      $GLOBALS['pad_no_boot_shutdown'] = TRUE;

      exit;

    }

  }


?>