<?php

  // Function build for repunit: pqRepunit($n) is n ones in a row, (10^n - 1) / 9 - 1, 11, 111,
  // 1111, ... from n = 1, 0 at position 0. The nineteenth is the last inside the integer
  // range; a later position answers a float out of it, which ends the build. A position below
  // 0, or between two, has no term.

  function pqRepunit ( $n ) {

    if ( ! pqBoolWhole ( $n ) or $n < 0 )
      return FALSE;

    if ( $n > 19 )
      return (float) PHP_INT_MAX * 2;

    return (int) str_repeat ( '1', (int) $n );

  }

?>
