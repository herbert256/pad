<?php

  // Function build for octahedral: pqOctahedral($n) = n(2n^2 + 1)/3, the number of spheres
  // in an octahedral pile of n layers - 1, 6, 19, 44, 85, 146, 231, ...

  function pqOctahedral ($n) {

    // A position below the first one, or between two, has no term: the formula answered there
    // with numbers that are no terms of the sequence - pentagonal at -2 and -1 gave 7 and 2.

    if ( ! pqBoolWhole ( $n ) or $n < 0 )
      return FALSE;


    return pqProduct ( [ $n, 2 * $n * $n + 1 ], 3 );

  }

?>
