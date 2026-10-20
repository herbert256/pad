<?php

  // Function build for pentagonal: pqPentagonal($n) is the term at position n, n(3n - 1)/2, 0, 1, 5, 12, 22, ...
  //
  // loop.php makes the run's terms through it, and as a formula of the position it is the
  // build build='function' runs and what sequence:pentagonal(n) answers from for a position past the
  // reach of a walk. A position below the first one, 0, or between two, has no term.

  function pqPentagonal ( $n ) {

    if ( ! pqBoolWhole ( $n ) or $n < 0 )
      return FALSE;

    $n = (int) ( $n + 0 );

    return pqProduct ( [ $n, 3 * $n - 1 ], 2 );

  }

?>
