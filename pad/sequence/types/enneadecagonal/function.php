<?php

  // Build strategy 'function' for the enneadecagonal sequence: pqEnneadecagonal($n) returns
  // the nth nineteen-sided figurate number, n(17n - 15)/2 - 1, 19, 54, 106, 175, 261, ...

  function pqEnneadecagonal ($n) {

    // A position below the first one, or between two, has no term: the formula answered there
    // with numbers that are no terms of the sequence - pentagonal at -2 and -1 gave 7 and 2.

    if ( ! pqBoolWhole ( $n ) or $n < 0 )
      return FALSE;


    return pqProduct ( [ $n, 17 * $n - 15 ], 2 );

  }

?>
