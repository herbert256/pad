<?php

  // Function build for sigma: pqSigma($n) is the sum of the divisors of n - 1, 3, 4, 7, 6, 12,
  // 8, 15, 13, 18, ... from n = 1 - made from its prime factors by pqSigmaOf() in
  // _lib/factors.php. A sum past the integer range is the float it overflows to, which ends
  // the build. A position below 1, or between two, has no term.

  include_once PQ . '_lib/factors.php';

  function pqSigma ( $n ) {

    if ( ! pqBoolWhole ( $n ) or $n < 1 )
      return FALSE;

    return pqSigmaOf ( $n );

  }

?>
