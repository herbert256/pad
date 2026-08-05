<?php

  // Evaluates a play built by the 'order' strategy, where a term depends on all the terms
  // before it and so cannot be computed in isolation.
  //
  // Reads the candidate-th term (1-based) from the type's precomputed PADxxx constant when
  // that table reaches far enough, and otherwise falls back to generating the sequence once
  // through pqArray() with sole=, which costs a nested build. FALSE if neither yields one.

  if ( defined ( "PAD$pqSeq" ) and isset ( constant ( "PAD$pqSeq" ) [$pqLoop-1] ) )
    return constant ( "PAD$pqSeq" ) [$pqLoop-1];

  $pqTmp = pqArray ( $pqSeq, $pqParm, "sole=$pqLoop" );

  return  ( isset ( $pqTmp [0]) ) ? $pqTmp [0] : FALSE;

?>