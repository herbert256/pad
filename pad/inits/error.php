<?php
  
  if ( $pError_action == 'boot' ) 
    return;

  restore_error_handler ();
  restore_exception_handler ();
  $pSkip_boot_shutdown = TRUE;

  if ( $pError_action == 'php' ) {
    ini_set ('display_errors', $pDisplay_errors);
    error_reporting ( $pError_reporting );
    return;
  }

  set_error_handler          ( 'pError_handler'   );
  set_exception_handler      ( 'pError_exception' );
  register_shutdown_function ( 'pError_shutdown'  );

  pError_reporting ( $pError_level );

 
?>