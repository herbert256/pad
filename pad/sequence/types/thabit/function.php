<?php

  // Function build for thabit: pqThabit($n) is the Thabit number 3 * 2^(n-1) - 1 - 2, 5, 11,
  // 23, 47, 95, 191, ... from n = 1, the first, 3 * 2^0 - 1, at position 1. From n = 63 the
  // term leaves the integer range as a float, which ends the build. A position below 1, or
  // between two, has no term.

  function pqThabit ( $n ) {

    if ( ! pqBoolWhole ( $n ) or $n < 1 )
      return FALSE;

    $n = (int) $n;

    if ( $n > 63 )
      return (float) PHP_INT_MAX * 2;

    return 3 * ( 1 << ( $n - 1 ) ) - 1;

  }

?>
