<?php

  // Evaluates a play built by the 'build' strategy: the type's build.php returns its whole
  // term list, from which the candidate-th term (1-based) is returned - FALSE when the
  // list does not reach that far.

  $pqTmp = include PT . "$pqSeq/build.php";

  return ( isset ( $pqTmp [$pqLoop-1]) ) ? $pqTmp [$pqLoop-1] : FALSE;

?>