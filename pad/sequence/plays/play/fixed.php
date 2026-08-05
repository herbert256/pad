<?php

  // Evaluates a play built by the 'fixed' strategy: the type's fixed.php returns its
  // ready-made term list, from which the candidate-th term (1-based) is returned - FALSE
  // when the list does not reach that far.

  $pqTmp = include PT . "$pqSeq/fixed.php";

  return ( isset ( $pqTmp [$pqLoop-1]) ) ? $pqTmp [$pqLoop-1] : FALSE;

?>