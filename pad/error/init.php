<?php
  
  if ( isset ( $padErrorInit ) )
    return;

  $padErrorInit = TRUE;

  include pad . "error/$padErrorAction/error.php";
 
?>