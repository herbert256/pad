<?php
 
  global $pqSetAction, $pqSetParms;

  $pqSetAction = $name;
  $pqSetParms  = $parm;
  
  return include 'sequence/action.php';

?>