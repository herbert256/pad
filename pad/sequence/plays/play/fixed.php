<?php

  $pqTmp = include PT . "$pqSeq/fixed.php";

  return ( isset ( $pqTmp [$pqLoop-1]) ) ? $pqTmp [$pqLoop-1] : FALSE;

?>
