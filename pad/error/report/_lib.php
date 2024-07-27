<?php

  function padErrorGo ( $error, $file, $line ) {
   
    error_log ( "$file:$line $error", 4 );
 
  }

?>