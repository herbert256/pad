<?php
  
  $pqTmp = include PQ . "types/$pqSeq/fixed.php";
  
  return ( isset ( $pqTmp [$pqLoop-1]) ) ? $pqTmp [$pqLoop-1] : FALSE;

?>