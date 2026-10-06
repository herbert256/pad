<?php

  // Build strategy 'function' for the hexagonal sequence: pqHexagonal($n) returns the nth
  // six-sided figurate number, n(2n - 1) - 1, 6, 15, 28, 45, 66, 91, ... These are also
  // the odd-indexed triangular numbers.

  function pqHexagonal ($n) {

    // A position below the first one, or between two, has no term: the formula answered there
    // with numbers that are no terms of the sequence - pentagonal at -2 and -1 gave 7 and 2.

    if ( ! pqBoolWhole ( $n ) or $n < 0 )
      return FALSE;


    return $n * (2 * $n - 1);

  }

?>
