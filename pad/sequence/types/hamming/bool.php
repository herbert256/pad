<?php

  // Membership predicate and generation path for hamming: pqBoolHamming($n) is TRUE when n has
  // no prime factor above 5 - 1, 2, 3, 4, 5, 6, 8, 9, 10, 12, 15, 16, ... - the regular
  // numbers of 2^i * 3^j * 5^k. 2, 3 and 5 are divided out and 1 must be left. The type's
  // only build, so the whole range is filtered through it: they thin out, 175 of them up to
  // 10,000, the reach of a run by default.

  function pqBoolHamming ( $n, $p=0 ) {

    if ( ! pqBoolWhole ( $n ) or $n < 1 )
      return FALSE;

    $n = (int) $n;

    foreach ( [ 2, 3, 5 ] as $prime )
      while ( $n % $prime == 0 )
        $n = intdiv ( $n, $prime );

    return $n == 1;

  }

?>
