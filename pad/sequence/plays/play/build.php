<?php

  // Evaluates a play built by the 'build' strategy: the type's build.php returns its whole
  // term list, from which the candidate-th term (1-based) is returned - FALSE when the
  // list does not reach that far.

  // A position is a whole number: a word or a fraction among the values a play is handed has
  // no term here, where it ended the request on the arithmetic or the array key it made.

  if ( ! pqBoolWhole ( $pqLoop ) )
    return FALSE;

  // The list is made once per sequence and parameter, under the key build/check.php keeps
  // its index under, pqListKey (): made for every candidate, the list type filtered the whole
  // of a list=pull:b for each value a make play was handed, with the square of its length.

  $pqPlayKey = pqListKey ( 'build', $pqSeq, $pqParm, $pqInc, $padParm ?? '' );

  if ( ! isset ( $padSeqPlayList [$pqPlayKey] ) )
    $padSeqPlayList [$pqPlayKey] = include PT . "$pqSeq/build.php";

  return ( isset ( $padSeqPlayList [$pqPlayKey] [$pqLoop-1]) ) ? $padSeqPlayList [$pqPlayKey] [$pqLoop-1] : FALSE;

?>
