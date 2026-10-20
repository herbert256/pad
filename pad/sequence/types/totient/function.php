<?php

  // Function build for totient: pqTotient($n) is Euler's totient of n, how many of the whole
  // numbers from 1 to n share no factor with it - 1, 1, 2, 2, 4, 2, 6, 4, 6, 4, ... from n = 1:
  // n times (1 - 1/p) for every prime p dividing it, the primes from pqFactors() in
  // _lib/factors.php. A position below 1, or between two, has no term.

  include_once PQ . '_lib/factors.php';

  function pqTotient ( $n ) {

    if ( ! pqBoolWhole ( $n ) or $n < 1 )
      return FALSE;

    $phi = (int) $n;

    foreach ( pqFactors ( $n ) as $p => $e )
      $phi = intdiv ( $phi, $p ) * ( $p - 1 );

    return $phi;

  }

?>
