<?php

  // Evaluates a play built by the 'order' strategy, where a term depends on all the terms
  // before it and so cannot be computed in isolation.
  //
  // Reads the candidate-th term (1-based) from the type's precomputed PADxxx constant when
  // that table reaches far enough, and otherwise falls back to generating the sequence once
  // through pqTerms() with sole=, which costs a build of its own. FALSE if neither yields one.

  // A position is a whole number: a word or a fraction among the values a play is handed has
  // no term here, where it ended the request on the arithmetic or the array key it made.

  if ( ! pqBoolWhole ( $pqLoop ) )
    return FALSE;

  if ( defined ( "PAD$pqSeq" ) and isset ( constant ( "PAD$pqSeq" ) [$pqLoop-1] ) )
    return constant ( "PAD$pqSeq" ) [$pqLoop-1];

  $pqTmp = pqTerms ( $pqSeq, $pqParm, [ 'sole' => $pqLoop ] );

  return  ( isset ( $pqTmp [0]) ) ? $pqTmp [0] : FALSE;

?>
