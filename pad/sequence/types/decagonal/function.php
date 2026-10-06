<?php

  // Build strategy 'function' for the decagonal sequence: pqDecagonal($n) returns the nth
  // ten-sided figurate number, 4n^2 - 3n - 1, 10, 27, 52, 85, 126, ...

  function pqDecagonal ($n) {

    // A position below the first one, or between two, has no term: the formula answered there
    // with numbers that are no terms of the sequence - pentagonal at -2 and -1 gave 7 and 2.

    if ( ! pqBoolWhole ( $n ) or $n < 0 )
      return FALSE;


    return $n * ( 4 * $n - 3 );

  }

?>
