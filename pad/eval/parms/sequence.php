<?php
 
  $pqSetSeq  = $name;
  $pqSetParm = array_shift ( $parm );

  $pqSetParms = $parm;

  return include 'sequence/sequence.php';

?>