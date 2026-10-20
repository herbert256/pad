<?php

  // Membership test for sigma: pqBoolSigma($x) is TRUE when x is the sum of the divisors of
  // some n - 1, 3, 4, 6, 7, 8, 12, 13, 14, 15, ... sigma multiplies over prime powers, and the
  // power p^k adds 1 + p + ... + p^k, so x is a product of such sums for rising primes: each
  // is a divisor of x, and the search runs over the divisors of x alone, the primes that make
  // a divisor such a sum worked out the first time the search meets it.
  //
  // Not the type's build: pqBuild() ranks function.php before bool.php.

  include_once PQ . '_lib/factors.php';

  function pqBoolSigma ( $x, $p=0 ) {

    if ( ! pqBoolWhole ( $x ) or $x < 1 )
      return FALSE;

    $x = (int) $x;

    $sums = [];

    return pqSigmaHas ( $x, 2, pqDivisorList ( $x ), $sums );

  }

  // Whether $m is the sigma of a number whose primes are all $least or more. $divisors are
  // those of the value asked about, which hold every divisor of $m; $sums keeps the primes of
  // each divisor once worked out.

  function pqSigmaHas ( $m, $least, $divisors, &$sums ) {

    if ( $m == 1 )
      return TRUE;

    foreach ( $divisors as $d ) {

      if ( $d > $m )
        break;

      if ( $d < 3 or $m % $d )
        continue;

      foreach ( $sums [$d] ??= pqSigmaPrimes ( $d ) as $prime )
        if ( $prime >= $least and pqSigmaHas ( intdiv ( $m, $d ), $prime + 1, $divisors, $sums ) )
          return TRUE;

    }

    return FALSE;

  }

  // The primes p with 1 + p + ... + p^k = $d for a k of 1 or more - there can be two: 31 is
  // 1 + 2 + 4 + 8 + 16 and 1 + 5 + 25. For a k of 2 or more p is the whole part of the k-th
  // root of d, which the float root lands on or next to; the sum is formed in floating point
  // first, and multiplied out exactly only for a p it comes near d for.

  function pqSigmaPrimes ( $d ) {

    $primes = [];

    if ( pqBoolPrime ( $d - 1 ) )
      $primes [] = $d - 1;

    for ( $k = 2; $k < 63 and 2 ** ( $k + 1 ) - 1 <= $d; $k++ ) {

      $root = (int) ( $d ** ( 1 / $k ) );

      for ( $prime = max ( 2, $root - 1 ); $prime <= $root + 1; $prime++ ) {

        if ( abs ( ( (float) $prime ** ( $k + 1 ) - 1 ) / ( $prime - 1 ) - $d ) > $d / 1e9 )
          continue;

        for ( $sum = 1, $power = 1, $i = 1; $i <= $k and $sum < $d; $i++ )
          $sum += $power *= $prime;

        if ( $i > $k and $sum === $d and pqBoolPrime ( $prime ) )
          $primes [] = $prime;

      }

    }

    return $primes;

  }

?>
