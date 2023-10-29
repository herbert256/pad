<?php
  
  if ( $padErrorAction == 'boot' ) 
    return;

  restore_error_handler ();
  restore_exception_handler ();
  $padSkipBootShutdown = TRUE;

  if ( $padErrorAction == 'php' ) {
    ini_set ('display_errors', $padDisplayErrors);
    error_reporting ( $padErrorReporting );
    return;
  }

  set_error_handler          ( 'padErrorHandler'   );
  set_exception_handler      ( 'padErrorException' );
  register_shutdown_function ( 'padErrorShutdown'  );

  padErrorReporting ( $padErrorLevel );

?>