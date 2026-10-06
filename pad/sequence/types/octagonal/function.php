<?php

  // Function build for octagonal: pqOctagonal($n) = 3n^2 - 2n, the nth octagonal number,
  // 1, 8, 21, 40, 65, 96, 133, ...

  function pqOctagonal ($n) {

    // A position below the first one, or between two, has no term: the formula answered there
    // with numbers that are no terms of the sequence - pentagonal at -2 and -1 gave 7 and 2.

    if ( ! pqBoolWhole ( $n ) or $n < 0 )
      return FALSE;


    return $n * ( 3 * $n - 2 );

  }

?>
