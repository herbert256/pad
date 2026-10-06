<?php

  // Build strategy 'function' for the gnomonic sequence: pqGnomonic($n) returns 2n - 1, the
  // odd numbers 1, 3, 5, 7, 9, ... - the L-shaped gnomon that has to be added to one square
  // number to reach the next.

  function pqGnomonic ($n) {

    // A position below the first one, or between two, has no term: the formula answered there
    // with numbers that are no terms of the sequence - pentagonal at -2 and -1 gave 7 and 2.

    if ( ! pqBoolWhole ( $n ) or $n < 1 )
      return FALSE;


    return 2 * $n -1;

  }

?>
