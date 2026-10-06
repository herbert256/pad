<?php

  // Make build for oeis, the strategy pqBuild() picks for this type: returns the $pqLoop'th
  // term of the OEIS sequence named by the parameter, or FALSE once the table runs out.
  // build/one.php reads FALSE as a candidate without a term, not as the end of the build, so
  // a run past the last term ends on its rows, its to= or its try limit.
  //
  // Same table as oeis/fixed.php, read a term at a time, so from/to/increment index into the
  // sequence. The term list is 0-indexed, hence $pqLoop-1. Asking pqOeis() once per term
  // costs one lookup for the whole build, since it keeps the sequence last asked for.

  include_once PT . 'oeis/read.php';

  // A position is a whole number: a word or a fraction among the values a play is handed has
  // no term here, where it ended the request on the arithmetic or the array key it made.

  if ( ! pqBoolWhole ( $pqLoop ) )
    return FALSE;

  $pqOeisSeq = pqOeis ( $pqParm );

  if ( isset ( $pqOeisSeq [$pqLoop-1] ) )
    return $pqOeisSeq [$pqLoop-1];
  else
    return FALSE;

?>
