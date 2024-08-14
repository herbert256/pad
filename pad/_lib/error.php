<?php


  function padError ($error) {
 
    $GLOBALS ['catch'] .= '-E';

    extract ( debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS, 1) [0] );

    padErrorGo ( 'PAD: ' . $error, $file, $line ); 

    return FALSE; 
 
  }


  function padErrorRestoreBoot () {

    restore_error_handler ();
    restore_exception_handler ();
    $GLOBALS ['padSkipBootShutdown'] = TRUE;

  }


  function padErrorGet ( $e ) {

    return $e->getFile() . ':' .  $e->getLine() . ' ' . $e->getMessage() ;

  }


  function padErrorThrow ( $severity, $message, $filename, $lineno ) {

    if ( $GLOBALS ['padErrorAction'] == 'ignore' ) 
      return FALSE;

    if ( error_reporting() & $severity )
      throw new ErrorException ( $message, 0, $severity, $filename, $lineno );

  }
  

?>