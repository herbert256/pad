<?php

  // Function build for cubic: pqCubic($n) is the term at position n, n^3, 0, 1, 8, 27, 64, ...
  //
  // loop.php makes the run's terms through it, and as a formula of the position it is the
  // build build='function' runs and what sequence:cubic(n) answers from for a position past the
  // reach of a walk. A position below the first one, 0, or between two, has no term.

  function pqCubic ( $n ) {

    if ( ! pqBoolWhole ( $n ) or $n < 0 )
      return FALSE;

    $n = (int) ( $n + 0 );

    return $n ** 3;

  }

?>
