<?php
 

  function pad_error ( $error ) {

    try {
  
      extract ( debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS, 1) [0] );

      if ( $GLOBALS['pad_error_action'] == 'php' )
        throw new ErrorException ($error, 0, E_ERROR, $file, $line);

      return pad_error_go ( 'USER: ' . $error, $file, $line );
    
    } catch (Exception $e) {

      return pad_error_error ( $e->getMessage(), $e->getFile(), $e->getLine() );

    }
 
  }


  function pad_error_handler ( $type, $error, $file, $line ) {
 
    try {
  
      if ( error_reporting() & $type )
        return pad_error_go ( 'ERROR: ' . $error, $file, $line );
    
    } catch (Exception $e) {

      return pad_error_error ( $e->getMessage(), $e->getFile(), $e->getLine() );

    }

  }


  function pad_error_exception ( $error ) {

    try {

      return pad_error_go ( 'EXCEPTION: ' . $error->getMessage() , $error->getFile(), $error->getLine() );
    
    } catch (Exception $e) {

      return pad_error_error ( $e->getMessage(), $e->getFile(), $e->getLine() );

    }

  }
  

  function pad_error_shutdown () {

    try {
  
      if ( $GLOBALS['pad_exit'] == 9 )
        return;

      if ( $GLOBALS['pad_exit'] == 2 ) {
        $error = error_get_last ();
        return pad_error_error ( $error['message']??'' , $error['file']??'', $error['line']??'' );
      }

      $error = error_get_last ();
      if ($error !== NULL)
        return pad_error_go ( 'SHUTDOWN: ' . $error['message'] , $error['file'], $error['line'] );
    
    } catch (Exception $e) {

      return pad_error_error ( $e->getMessage(), $e->getFile(), $e->getLine() );

    }

  }


  function pad_error_go ($error, $file, $line) {
 
    try {

      if ( $GLOBALS['pad_exit'] <> 1 ) 
        return pad_error_error ( $error, $file, $line );

      $GLOBALS['pad_exit'] = 2;

      $id = $GLOBALS['PADREQID'] ?? uniqid();

      $msg = "$file:$line $error";
      $msg = str_replace (PAD_APP,  'APP:', $msg);
      $msg = str_replace (PAD_HOME, 'PAD:', $msg);

      if ( $GLOBALS['pad_error_server'] ) 
        error_log ("[PAD] $id $msg", 4);   

      if ( $GLOBALS['pad_track_errors'] ) 
        pad_track_vars ("errors/$id.html", $msg);

      if ( $GLOBALS['pad_error_action'] == 'none') {
        $GLOBALS['pad_exit'] = 1;
        return FALSE;
      }

      pad_trace ('error/error', $msg);   

      pad_empty_buffers ();

      if ( $GLOBALS['pad_error_action'] == 'boot' ) 
        pad_boot_error_go ($error, $file, $line);

      if ( ! headers_sent() )
        header ( 'HTTP/1.0 500 Internal Server Error' );

      if ( $GLOBALS['pad_error_action'] == 'abort')  {
        echo "Error: $id";
        pad_exit ();
      }

      if ( $GLOBALS['pad_error_action'] == 'pad') {

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

      error_log ("[PAD] ERROR-ERROR: $file:$line $error", 4);  

      if ( $GLOBALS['pad_error_action'] == 'none') 
        return FALSE;

      echo "ERROR IN ERROR";

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