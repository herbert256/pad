<?php

  // Membership predicate and generation path for amicable: pqBoolAmicable($n) is TRUE when the
  // divisors of n below n add up to another number m whose own divisors below it add up to n
  // again - 220, 284, 1184, 1210, 2620, 2924, 5020, 5564, 6232, 6368, ...: both of a pair are
  // in it. A perfect number is its own partner and is none. The sums come from pqSigmaOf() in
  // _lib/factors.php; a partner past the integer range is out of reach. The type's only
  // build, so the whole range is filtered through it.

  include_once PQ . '_lib/factors.php';

  function pqBoolAmicable ( $n, $p=0 ) {

    if ( ! pqBoolWhole ( $n ) or $n < 2 )
      return FALSE;

    $n       = (int) $n;
    $partner = pqSigmaOf ( $n ) - $n;

    if ( ! is_int ( $partner ) or $partner == $n or $partner < 2 )
      return FALSE;

    return pqSigmaOf ( $partner ) - $partner === $n;

  }

?>
