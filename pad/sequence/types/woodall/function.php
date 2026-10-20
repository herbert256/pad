<?php

  // Function build for woodall: pqWoodall($n) is the Woodall number n * 2^n - 1 - 1, 7, 23, 63,
  // 159, 383, 895, ... from n = 1 - the cullen numbers' counterpart. From n = 58 the product
  // leaves the integer range as a float, which ends the build, and past n = 62 the shift has
  // no answer, so a far position answers a float out of the range as well. A position below
  // 1, or between two, has no term.

  function pqWoodall ( $n ) {

    if ( ! pqBoolWhole ( $n ) or $n < 1 )
      return FALSE;

    $n = (int) $n;

    if ( $n > 62 )
      return (float) PHP_INT_MAX * 2;

    return ( 1 << $n ) * $n - 1;

  }

?>
