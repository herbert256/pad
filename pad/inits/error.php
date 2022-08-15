<?php
  
  if ( $padError_action == 'boot' ) 
    return;

  restore_error_handler ();
  restore_exception_handler ();
  $padSkip_boot_shutdown = TRUE;

  if ( $padError_action == 'php' ) {
    ini_set ('display_errors', $padDisplay_errors);
    error_reporting ( $padError_reporting );
    return;
  }

  set_error_handler          ( 'pError_handler'   );
  set_exception_handler      ( 'pError_exception' );
  register_shutdown_function ( 'pError_shutdown'  );

  pError_reporting ( $padError_level );

 
?>