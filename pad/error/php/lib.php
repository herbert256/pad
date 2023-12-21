<?php
  
  function padError ($error) {
 
    extract ( debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS, 1) [0] );

    if ( error_reporting() & $type )
      trigger_error ("$file:$line $error", E_USER_ERROR);
      
    return FALSE;

  }

?>