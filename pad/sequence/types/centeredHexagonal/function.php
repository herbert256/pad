<?php

  // Function build for centeredHexagonal: pqCenteredHexagonal($n) is the nth centered
  // hexagonal number, 3n(n - 1) + 1 - 1, 7, 19, 37, 61, 91, 127, ... from n = 1. A term past
  // the integer range is the float it overflows to, which ends the build. A position below 1,
  // or between two, has no term.

  function pqCenteredHexagonal ( $n ) {

    if ( ! pqBoolWhole ( $n ) or $n < 1 )
      return FALSE;

    $n = (int) $n;

    return 3 * $n * ( $n - 1 ) + 1;

  }

?>
