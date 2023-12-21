<?php
  

  function padErrorRestoreBoot () {

    restore_error_handler ();
    restore_exception_handler ();
    $GLOBALS ['padSkipShutdown'] = TRUE;

  }


  function padErrorReporting ( ) {

    $none    = (int) 0;
    $error   = (int) $none    | E_ERROR | E_USER_ERROR | E_CORE_ERROR | E_COMPILE_ERROR | E_PARSE;
    $warning = (int) $error   | E_RECOVERABLE_ERROR | E_WARNING | E_USER_WARNING | E_CORE_WARNING | E_COMPILE_WARNING;
    $notice  = (int) $warning | E_NOTICE | E_USER_NOTICE;
    $all     = (int) $notice  | E_DEPRECATED | E_USER_DEPRECATED | E_STRICT;

    $level = $GLOBALS ['padErrorLevel'];

    error_reporting ( $$level );
    
  }


  function padErrorThrow ( $severity, $message, $filename, $lineno ) {

    if ( $GLOBALS ['padErrorAction'] == 'ignore' ) 
      return FALSE;

    throw new ErrorException ( $message, 0, $severity, $filename, $lineno );

  }


  function padThrow ( $message ) {

    if ( $GLOBALS ['padErrorAction'] == 'ignore' ) 
      return FALSE;

    throw new Exception ( $message );

  }


?>