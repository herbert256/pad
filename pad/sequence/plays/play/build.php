<?php

  $pqTmp = include PT . "$pqSeq/build.php";

  return ( isset ( $pqTmp [$pqLoop-1]) ) ? $pqTmp [$pqLoop-1] : FALSE;

?>