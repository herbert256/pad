<?php

  // Build strategy 'loop' for the icosihenagonal sequence: each term is the nth twenty-one
  // sided figurate number, n(19n - 17)/2 - 1, 21, 60, 118, 195, 291, ... The other polygonal
  // types define a pqXxx() function; this one computes the term inline from $pqLoop.

  // A position below the first one, or between two, has no term: the formula answered there
  // with numbers that are no terms of the sequence - pentagonal at -2 and -1 gave 7 and 2.

  if ( ! pqBoolWhole ( $pqLoop ) or $pqLoop < 0 )
    return FALSE;

  return pqProduct ( [ $pqLoop, 19 * $pqLoop - 17 ], 2 );

?>
