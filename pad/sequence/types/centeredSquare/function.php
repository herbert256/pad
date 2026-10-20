<?php

  // Function build for centeredSquare: pqCenteredSquare($n) is the nth centered square number,
  // 2n(n - 1) + 1 = n^2 + (n - 1)^2 - 1, 5, 13, 25, 41, 61, 85, ... from n = 1. A term past
  // the integer range is the float it overflows to, which ends the build. A position below 1,
  // or between two, has no term.

  function pqCenteredSquare ( $n ) {

    if ( ! pqBoolWhole ( $n ) or $n < 1 )
      return FALSE;

    $n = (int) $n;

    return 2 * $n * ( $n - 1 ) + 1;

  }

?>
