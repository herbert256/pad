<?php
 
  global $pqSetAction, $pqSetParms;

  $pqSetAction = $name;
  $pqSetParms  = [];
  
  return include 'sequence/action.php';

?>