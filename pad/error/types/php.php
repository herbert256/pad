<?php

  
  padErrorRestoreBoot ();

  ini_set ('display_errors', $padDisplayErrors);
  
  error_reporting ( $padErrorReporting );


  function padErrorGo ( $error, $file, $line ) {
 
    trigger_error ( "$file:$line $error", E_USER_ERROR );
      
    return FALSE;

  }


?>