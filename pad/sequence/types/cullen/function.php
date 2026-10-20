<?php

  // Function build for cullen: pqCullen($n) is the term at position n, n * 2^n + 1, 1, 3, 9, 25, 65, ...
  //
  // loop.php makes the run's terms through it, and as a formula of the position it is the
  // build build='function' runs and what sequence:cullen(n) answers from for a position past the
  // reach of a walk. A position below the first one, 0, or between two, has no term.

  function pqCullen ( $n ) {

    if ( ! pqBoolWhole ( $n ) or $n < 0 )
      return FALSE;

    $n = (int) ( $n + 0 );

    // Past n = 62 the shift has no answer - 1 << 64 is 0 - so a far position answers a float
    // out of the integer range, which ends the build; from n = 58 the product leaves the range
    // as a float by itself.

    if ( $n > 62 )
      return (float) PHP_INT_MAX * 2;

    return ( 1 << $n ) * $n + 1;

  }

?>
