<?php

  // Evaluates a play built by the 'build' strategy: the type's build.php returns its whole
  // term list, from which the candidate-th term (1-based) is returned - FALSE when the
  // list does not reach that far.

  // A position is a whole number: a word or a fraction among the values a play is handed has
  // no term here, where it ended the request on the arithmetic or the array key it made.

  if ( ! pqBoolWhole ( $pqLoop ) )
    return FALSE;

  $pqTmp = include PT . "$pqSeq/build.php";

  return ( isset ( $pqTmp [$pqLoop-1]) ) ? $pqTmp [$pqLoop-1] : FALSE;

?>
