<?php

  // Function build for biquadratic: pqBiquadratic($n) is the term at position n, n^4, 0, 1, 16, 81, 256, ...
  //
  // loop.php makes the run's terms through it, and as a formula of the position it is the
  // build build='function' runs and what sequence:biquadratic(n) answers from for a position past the
  // reach of a walk. A position below the first one, 0, or between two, has no term.

  function pqBiquadratic ( $n ) {

    if ( ! pqBoolWhole ( $n ) or $n < 0 )
      return FALSE;

    $n = (int) ( $n + 0 );

    return $n ** 4;

  }

?>
