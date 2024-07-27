<?php


  function padErrorRestoreBoot () {

    restore_error_handler ();
    restore_exception_handler ();
    $GLOBALS ['padSkipBootShutdown'] = TRUE;

  }


  function padError ($error) {
 
    extract ( debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS, 1) [0] );

    padErrorGo ( 'PAD: ' . $error, $file, $line ); 

    return FALSE; 
 
  }


  
?>