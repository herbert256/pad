<?php

  // Membership predicate and generation path for deficient: pqBoolDeficient($n) is TRUE when
  // the divisors of n below n add up to less than n - when the sum of them all, pqSigmaOf() in
  // _lib/factors.php, stays under 2n - 1, 2, 3, 4, 5, 7, 8, 9, 10, 11, 13, ... Every prime and
  // every power of a prime is one. The type's only build, so the whole range is filtered
  // through it.

  include_once PQ . '_lib/factors.php';

  function pqBoolDeficient ( $n, $p=0 ) {

    if ( ! pqBoolWhole ( $n ) or $n < 1 )
      return FALSE;

    $n = (int) $n;

    return pqSigmaOf ( $n ) < 2 * $n;

  }

?>
