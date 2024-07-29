<?php
  
  include pad . "error/_lib.php";

  padErrorReporting   ( $padErrorLevel );
  padErrorRestoreBoot ();

  set_error_handler          ( 'padErrorHandler'   );
  set_exception_handler      ( 'padErrorException' );
  register_shutdown_function ( 'padErrorShutdown'  );
  
?>