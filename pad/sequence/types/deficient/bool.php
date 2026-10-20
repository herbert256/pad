<?php

  // Membership predicate and generation path for deficient: pqBoolDeficient($n) is TRUE when
  // the divisors of n below n add up to less than n - when the sum of them all stays under 2n,
  // as pqAbundance() in _lib/factors.php tells exactly over the whole integer range - 1, 2, 3,
  // 4, 5, 7, 8, 9, 10, 11, 13, ... Every prime and every power of a prime is one. The type's
  // only build, so the whole range is filtered through it. pqSigmaOf ( $n ) < 2 * $n compared
  // a float past the range with another, and 2^62, whose sum is PHP_INT_MAX, was no member.

  include_once PQ . '_lib/factors.php';

  function pqBoolDeficient ( $n, $p=0 ) {

    if ( ! pqBoolWhole ( $n ) or $n < 1 )
      return FALSE;

    $n = (int) $n;

    return pqAbundance ( $n ) == -1;

  }

?>
