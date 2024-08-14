<?php
  
  if ( isset ( $padErrorInit ) )
    return;

  $padErrorInit = TRUE;

  include pad . "error/types/$padErrorAction/_lib.php";
  include pad . "error/types/$padErrorAction/error.php";
 
?>