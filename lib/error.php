<?php
 

  function pad_error ( $error ) {

    try {
  
      if ( $GLOBALS['pad_exit'] <> 1 )
        pad_error_exit ( "pad_error: " . $error );

      extract ( debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS, 1) [0] );

      return pad_error_go ( 'USER: ' . $error, $file, $line );
    
    } catch (Exception $e) {

      pad_error_error ( $e->getMessage(), $e->getFile(), $e->getLine() );

    }
 
  }


  function pad_error_handler ( $type, $error, $file, $line ) {
 
    try {
 
      if ( $GLOBALS['pad_exit'] <> 1 )
        pad_error_exit ( "pad_error_handler: $file:$line $error" );

      if ( error_reporting() & $type )
        return pad_error_go ( 'ERROR: ' . $error, $file, $line );
    
    } catch (Exception $e) {

      pad_error_error ( $e->getMessage(), $e->getFile(), $e->getLine() );

    }

  }


  function pad_error_exception ( $error ) {

    try {

      if ( $GLOBALS['pad_exit'] <> 1 )
        pad_error_exit ( "pad_error_exception: " . $error->getMessage() );

      return pad_error_go ( 'EXCEPTION: ' . $error->getMessage() , $error->getFile(), $error->getLine() );
    
    } catch (Exception $e) {

      pad_error_error ( $e->getMessage(), $e->getFile(), $e->getLine() );

    }

  }
  

  function pad_error_shutdown () {

    try {  

      if ( $GLOBALS['pad_exit'] == 9 )
        exit;

      $error = error_get_last ();

      if ( $error === NULL ) 
        exit;
      elseif ( $GLOBALS['pad_exit'] == 1 )
        pad_error_go ( 'SHUTDOWN: ' . $error['message'] , $error['file'], $error['line'] );
      else
        pad_error_error ( $error['message'], $error['file'], $error['line'] );
    
    } catch (Exception $e) {

      pad_error_error ( $e->getMessage(), $e->getFile(), $e->getLine() );

    }

  }


  function pad_error_go ($error, $file, $line) {

    if ( $GLOBALS['pad_exit'] == 9 )
      pad_error_exit ( "pad_error_go: " . $error );

    try {

      if ( $GLOBALS['pad_exit'] == 1 ) 
        $GLOBALS['pad_exit'] = 2;
      else
        pad_error_error ( $error, $file, $line );

      $error = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '.', $error);
      if ( strlen($error) > 255 )
        $error = substr($error, 0, 255);

      pad_trace ('error', "$file:$line $error");   

      $id = $GLOBALS['PADREQID'] ?? uniqid();

      if ( $GLOBALS['pad_error_server'] and $GLOBALS['pad_error_action'] <> 'boot' ) 
        error_log ("[PAD] $id $file:$line $error", 4);   
      elseif ( $GLOBALS['pad_error_action'] == 'report' )
        error_log ("[PAD] $id $file:$line $error", 4);   

      if ( $GLOBALS['pad_error_dump'] or $GLOBALS['pad_error_action'] == 'report' ) 
        pad_track_vars ("errors/$id.html", "$file:$line $error");

      return pad_error_action ( $error, $file, $line );

    } catch (Exception $e) {

      pad_error_error ( $e->getMessage(), $e->getFile(), $e->getLine() );

    }

  }


  function pad_error_action ( $error, $file, $line ) {  

      global $pad_error_action;

      if ( $pad_error_action == 'boot' ) {

        pad_boot_error_go ( $error, $file, $line );

      } elseif ( $pad_error_action == 'none' ) {

        $GLOBALS['pad_exit'] = 1;
        return FALSE;

      } elseif ( $pad_error_action == 'report' ) {

        $GLOBALS['pad_exit'] = 1;
        return FALSE;

      } elseif ( $pad_error_action == 'abort') {

        pad_error_exitit ();

      } elseif ( $pad_error_action == 'stop') {

        $GLOBALS ['pad_stop'] = 500;
        include PAD_HOME . 'exits/stop.php';
      
      } elseif ( $pad_error_action == 'pad' ) {

        pad_error_pad ( $error, $file, $line );

      } elseif ( $pad_error_action == 'php' ) {

        throw new ErrorException ($error, 0, E_ERROR, $file, $line);

      } else {

        pad_dump ("Unknown error action: $file:$line $error");

      }

  }


  function pad_error_pad ( $error, $file, $line ) {

    if ( pad_local() )
      pad_dump ("$file:$line $error");

    if ( ! $GLOBALS['pad_error_dump'] ) 
      pad_track_vars ("errors/$id.html", "$file:$line $error");

    echo "Error: $id";
    $GLOBALS ['pad_sent'] = TRUE;             
      
    $GLOBALS ['pad_stop'] = 500;
    include PAD_HOME . 'exits/stop.php';

  }


  function pad_error_error ( $error, $file, $line ) {

    $GLOBALS ['pad_exit']             = 9;
    $GLOBALS ['pad_no_boot_shutdown'] = TRUE;

    $id = $GLOBALS['PADREQID'] ?? uniqid();

    try {

      if ( $GLOBALS['pad_exit'] == 9 )
        pad_error_exit ( "pad_error_error: " . $error );

      $error = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '.', $error);
      if ( strlen($error) > 255 )
        $error = substr($error, 0, 255);

      error_log ( "[PAD] $id - error-error: $file:$line $error", 4 );
      
      if ( ! headers_sent () )
        header ( 'HTTP/1.0 500 Internal Server Error' );

      echo "error-error: $id";

      if ( pad_local () )
        echo " - $file:$line $error";

    } catch (Exception $e) {

      echo 'wtf (error) -> ' . $id;

    }

    exit;

  }  


 function pad_error_exit ( $error ) {

    $GLOBALS ['pad_exit']             = 9;
    $GLOBALS ['pad_no_boot_shutdown'] = TRUE;

    $id = $GLOBALS['PADREQID'] ?? uniqid();

    try {

      $error = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '.', $error);
      if ( strlen($error) > 255 )
        $error = substr($error, 0, 255);

      error_log ( "[PAD] $id - error-exit: $error", 4 );
      
      if ( ! headers_sent () )
        header ( 'HTTP/1.0 500 Internal Server Error' );

      echo "error-exit: $id";

      if ( pad_local () )
        echo " - $error";

    } catch (Exception $e) {

      echo 'wtf (exit) -> ' . $id;

    }

    exit;

  }  


?>