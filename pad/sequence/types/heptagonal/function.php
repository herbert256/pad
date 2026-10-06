<?php

  // Build strategy 'function' for the heptagonal sequence: pqHeptagonal($n) returns the nth
  // seven-sided figurate number, n(5n - 3)/2 - 1, 7, 18, 34, 55, 81, 112, ...

  function pqHeptagonal ($n) {

    // A position below the first one, or between two, has no term: the formula answered there
    // with numbers that are no terms of the sequence - pentagonal at -2 and -1 gave 7 and 2.

    if ( ! pqBoolWhole ( $n ) or $n < 0 )
      return FALSE;


    return pqProduct ( [ $n, 5 * $n - 3 ], 2 );

  }

?>
