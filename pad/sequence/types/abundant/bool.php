<?php

  // Membership predicate and generation path for abundant: pqBoolAbundant($n) is TRUE when
  // the divisors of n below n add up to more than n - when the sum of them all, pqSigmaOf() in
  // _lib/factors.php, passes 2n - 12, 18, 20, 24, 30, 36, 40, 42, 48, 54, ... The type's only
  // build, so the whole range is filtered through it.

  include_once PQ . '_lib/factors.php';

  function pqBoolAbundant ( $n, $p=0 ) {

    if ( ! pqBoolWhole ( $n ) or $n < 1 )
      return FALSE;

    $n = (int) $n;

    return pqSigmaOf ( $n ) > 2 * $n;

  }

?>
