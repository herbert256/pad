<?php

  
  padErrorRestoreBoot ();

  ini_set ( 'display_errors', $padDisplayErrors );
  
  error_reporting ( $padErrorReporting );


  function padErrorGo ( $error, $file, $line ) {
 
    if PHP_VERSION_ID >= 80400
      throw new Exception ( "$file:$line $error" );
    else
      trigger_error ( "$file:$line $error", E_USER_ERROR );
      
    return FALSE;

  }


?>