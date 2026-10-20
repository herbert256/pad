<?php

  // Function build for icosihenagonal: pqIcosihenagonal($n) is the term at position n, n(19n - 17)/2, 0, 1, 21, 60, 118, ...
  //
  // loop.php makes the run's terms through it, and as a formula of the position it is the
  // build build='function' runs and what sequence:icosihenagonal(n) answers from for a position past the
  // reach of a walk. A position below the first one, 0, or between two, has no term.

  function pqIcosihenagonal ( $n ) {

    if ( ! pqBoolWhole ( $n ) or $n < 0 )
      return FALSE;

    $n = (int) ( $n + 0 );

    return pqProduct ( [ $n, 19 * $n - 17 ], 2 );

  }

?>
