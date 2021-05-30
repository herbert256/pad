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
  
  function pad_error_reporting ( $level ) {

    $none    = (int) 0;
    $error   = (int)            E_ERROR | E_USER_ERROR | E_CORE_ERROR | E_COMPILE_ERROR | E_PARSE;
    $warning = (int) $error   | E_RECOVERABLE_ERROR | E_WARNING | E_USER_WARNING | E_CORE_WARNING | E_COMPILE_WARNING;
    $notice  = (int) $warning | E_NOTICE | E_USER_NOTICE;
    $all     = (int) $notice  | E_DEPRECATED | E_USER_DEPRECATED | E_STRICT;

    error_reporting ( $$level );
    
  }
  
?>