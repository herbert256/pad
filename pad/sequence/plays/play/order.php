<?php

  // Evaluates a play built by the 'order' strategy, where a term depends on all the terms
  // before it and so cannot be computed in isolation.
  //
  // Reads the candidate-th term (1-based) from the type's precomputed PADxxx constant when
  // that table reaches far enough, and otherwise falls back to generating the sequence once
  // through pqTerms() with sole=, which costs a build of its own. FALSE if neither yields one.

  // A position is a whole number: a word or a fraction among the values a play is handed has
  // no term here, where it ended the request on the arithmetic or the array key it made.

  // The sequence starts at position 1: position 0 has no term, where it read the table at -1,
  // found none, and the run with sole=0 answered the first term - {sequence '0;1;2;3', make,
  // lucas} gave 1 1 3 4.

  if ( ! pqBoolWhole ( $pqLoop ) or $pqLoop < 1 )
    return FALSE;

  $pqOrderAt = (int) ( $pqLoop + 0 );

  if ( defined ( "PAD$pqSeq" ) and isset ( constant ( "PAD$pqSeq" ) [$pqOrderAt-1] ) )
    return constant ( "PAD$pqSeq" ) [$pqOrderAt-1];

  // Past the table the terms are made once for the request and kept, pqNth: made afresh for
  // every candidate, make golomb over 200 positions past 40000 cost 19 ms a candidate.

  return pqNth ( $pqSeq, $pqParm, $pqOrderAt );

?>
