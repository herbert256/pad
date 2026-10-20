<?php

  // Membership predicate and generation path for sphenic: pqBoolSphenic($n) is TRUE when n is
  // the product of three different primes - 30, 42, 66, 70, 78, 102, 105, 110, 114, 130, ...
  // The factors come from pqFactors() in _lib/factors.php. The type's only build, so the
  // whole range is filtered through it.

  include_once PQ . '_lib/factors.php';

  function pqBoolSphenic ( $n, $p=0 ) {

    if ( ! pqBoolWhole ( $n ) or $n < 30 )
      return FALSE;

    $factors = pqFactors ( $n );

    return count ( $factors ) == 3 and array_sum ( $factors ) == 3;

  }

?>
