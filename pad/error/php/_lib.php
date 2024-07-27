<?php
  
  function padErrorGo ( $error, $file, $line ) {
 
    trigger_error ( "$file:$line $error", E_USER_ERROR );
      
    return FALSE;

  }

?>