<?php

  // Function build for collatz: pqCollatz($n) is how many steps n takes to reach 1 when an even
  // number is halved and an odd one becomes 3n + 1 - 0, 1, 7, 2, 5, 8, 16, 3, 19, 6, ... from n
  // = 1. Every number tried so far comes to 1, all of them up to 2^68. A step that would take
  // 3n + 1 past the integer range answers a float out of it, which ends the build. A position
  // below 1, or between two, has no term.

  function pqCollatz ( $n ) {

    if ( ! pqBoolWhole ( $n ) or $n < 1 )
      return FALSE;

    for ( $n = (int) $n, $steps = 0; $n != 1; $steps++ )
      if ( $n % 2 == 0 )
        $n = intdiv ( $n, 2 );
      elseif ( $n > intdiv ( PHP_INT_MAX - 1, 3 ) )
        return (float) PHP_INT_MAX * 2;
      else
        $n = 3 * $n + 1;

    return $steps;

  }

?>
