<?php


  function padError ($error) {
 
    extract ( debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS, 1) [0] );

    if ( $GLOBALS ['padCatch'] )
      throw new \ErrorException ( $error, 0, 0, $file, $line );

    padErrorGo ( 'PAD: ' . $error, $file, $line ); 

    return FALSE; 
 
  }


  function padErrorRestoreBoot () {

    restore_error_handler ();
    restore_exception_handler ();
    $GLOBALS ['padBootShutdown'] = TRUE;

  }


  function padErrorGet ( $e ) {

    return $e->getFile() . ':' .  $e->getLine() . ' ' . $e->getMessage() ;

  }


?>