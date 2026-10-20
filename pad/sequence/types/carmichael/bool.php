<?php

  // Membership predicate and generation path for carmichael: pqBoolCarmichael($n) is TRUE for
  // a composite n that passes Fermat's test for every base it shares no factor with - 561,
  // 1105, 1729, 2465, 2821, 6601, 8911, 10585, ... By Korselt's criterion that is an odd n
  // with three or more prime factors, none of them twice, and p - 1 dividing n - 1 for each
  // prime p of them; the factors come from pqFactors() in _lib/factors.php. The type's only
  // build, so the whole range is filtered through it - seven of them below 10,000, the reach
  // of a run by default.

  include_once PQ . '_lib/factors.php';

  function pqBoolCarmichael ( $n, $p=0 ) {

    if ( ! pqBoolWhole ( $n ) or $n < 561 or $n % 2 == 0 )
      return FALSE;

    $n       = (int) $n;
    $factors = pqFactors ( $n );

    if ( count ( $factors ) < 3 )
      return FALSE;

    foreach ( $factors as $prime => $e )
      if ( $e > 1 or ( $n - 1 ) % ( $prime - 1 ) )
        return FALSE;

    return TRUE;

  }

?>
