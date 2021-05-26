<?php
 

  function pad_error ( $error ) {

    try {
  
      extract ( debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS, 1) [0] );

      if ( $GLOBALS['pad_error_action'] == 'php' )
        throw new ErrorException ($error, 0, E_ERROR, $file, $line);

      return pad_error_go ( 'USER: ' . $error, $file, $line );
    
    } catch (Exception $e) {

      pad_boot_error ( $e->getMessage(), $e->getFile(), $e->getLine() );

    }
 
  }


  function pad_error_handler ( $type, $error, $file, $line ) {
 
    try {
  
      if ( error_reporting() & $type )
        return pad_error_go ( 'ERROR: ' . $error, $file, $line );
    
    } catch (Exception $e) {

      pad_boot_error ( $e->getMessage(), $e->getFile(), $e->getLine() );

    }

  }


  function pad_error_exception ( $error ) {

    try {

      return pad_error_go ( 'EXCEPTION: ' . $error->getMessage() , $error->getFile(), $error->getLine() );
    
    } catch (Exception $e) {

      pad_boot_error ( $e->getMessage(), $e->getFile(), $e->getLine() );

    }

  }
  

  function pad_error_shutdown () {

    try {
  
      if ( $GLOBALS['pad_exit'] == 9 )
        return;

      $error = error_get_last ();

      if ( $error !== NULL)
        if ( $GLOBALS['pad_exit'] == 2 )
          pad_boot_error ( $error['message'], $error['file'], $error['line'] );
        else
          return pad_error_go ( 'SHUTDOWN: ' . $error['message'] , $error['file'], $error['line'] );
    
    } catch (Exception $e) {

      pad_boot_error ( $e->getMessage(), $e->getFile(), $e->getLine() );

    }

  }


  function pad_error_go ($error, $file, $line) {
 
    try {

      if ( $GLOBALS['pad_exit'] == 1 ) 
        $GLOBALS['pad_exit'] = 2;
      else
        pad_boot_error ( $error, $file, $line );

      pad_trace ('error', "$file:$line $error");   

      $id  = $GLOBALS['PADREQID'] ?? uniqid();

      if ( $GLOBALS['pad_error_server'] ) 
        error_log ("[PAD] $id $file:$line $error", 4);   

      if ( $GLOBALS['pad_track_errors'] ) 
        pad_track_vars ("errors/$id.html", "$file:$line $error");

      if ( $GLOBALS['pad_error_action'] == 'none' ) {
        $GLOBALS['pad_exit'] = 1;
        return FALSE;
      }

      pad_empty_buffers ();

      if ( $GLOBALS['pad_error_action'] == 'boot' ) 
        pad_boot_error ($error, $file, $line);

      if ( ! headers_sent() )
        header ( 'HTTP/1.0 500 Internal Server Error' );

      if ( $GLOBALS['pad_error_action'] == 'abort')  {
        echo "Error: $id";
        pad_exit ();
      }

      if ( $GLOBALS['pad_error_action'] == 'pad') {

        if ( pad_local() )
          echo "<pre><hr><b>$file:$line $error</b><hr></pre>";
        else 
          echo "Error: $id";

        $GLOBALS ['pad_sent'] = TRUE;

        pad_dump ();
                     
      }
      
      $GLOBALS ['pad_stop'] = 500;
      include PAD_HOME . 'exits/stop.php';

    } catch (Exception $e) {

      pad_boot_error ( $e->getMessage(), $e->getFile(), $e->getLine() );

    }

  }


?>