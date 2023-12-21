<?php
  
  include_once pad . "error/$padErrorAction/lib.php";

  padErrorReporting   ();
  padErrorRestoreBoot ();

  set_error_handler          ( 'padErrorHandler'   );
  set_exception_handler      ( 'padErrorException' );
  register_shutdown_function ( 'padErrorShutdown'  );
  
?>