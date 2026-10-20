<?php

  // Function build for star: pqStar($n) is the nth star number, the dots of a six-pointed
  // star, 6n(n - 1) + 1 - 1, 13, 37, 73, 121, 181, 253, ... from n = 1. A term past the
  // integer range is the float it overflows to, which ends the build. A position below 1, or
  // between two, has no term.

  function pqStar ( $n ) {

    if ( ! pqBoolWhole ( $n ) or $n < 1 )
      return FALSE;

    $n = (int) $n;

    return 6 * $n * ( $n - 1 ) + 1;

  }

?>
