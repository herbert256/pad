<?php

  // Build strategy 'function' for the heptadecagonal sequence: pqHeptadecagonal($n) returns
  // the nth seventeen-sided figurate number, n(15n - 13)/2 - 1, 17, 48, 94, 155, 231, ...

  function pqHeptadecagonal  ($n) {

    // A position below the first one, or between two, has no term: the formula answered there
    // with numbers that are no terms of the sequence - pentagonal at -2 and -1 gave 7 and 2.

    if ( ! pqBoolWhole ( $n ) or $n < 0 )
      return FALSE;


    return pqProduct ( [ $n, 15 * $n - 13 ], 2 );

  }

?>
