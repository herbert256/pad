<?php

  // Function build for stern: pqStern($n) is the nth term of Stern's diatomic sequence, a(0) =
  // 0, a(1) = 1, a(2n) = a(n) and a(2n+1) = a(n) + a(n+1) - 1, 1, 2, 1, 3, 2, 3, 1, 4, 3, ... from
  // n = 1, 0 at position 0. Two neighbours are carried down the binary digits of n, one step a
  // digit, so any position takes 63 steps at the most. a(n) / a(n+1) runs through every
  // positive fraction once. A position below 0, or between two, has no term.

  function pqStern ( $n ) {

    if ( ! pqBoolWhole ( $n ) or $n < 0 )
      return FALSE;

    for ( $n = (int) $n, $a = 1, $b = 0; $n > 0; $n >>= 1 )
      if ( $n & 1 )
        $b += $a;
      else
        $a += $b;

    return $b;

  }

?>
