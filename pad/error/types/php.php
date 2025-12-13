<?php

  
  padErrorRestoreBoot ();

  ini_set ( 'display_errors', $padDisplayErrors );
  
  error_reporting ( $padErrorReporting );


  function padErrorGo ( $error, $file, $line ) {
 
    throw new Exception ( "$file:$line $error" );
      
    return '';

  }


?>