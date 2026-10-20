<?php

  // Membership test for sigma: pqBoolSigma($x) is TRUE when x is the sum of the divisors of
  // some n - 1, 3, 4, 6, 7, 8, 12, 13, 14, 15, ... sigma multiplies over prime powers, and the
  // power p^k adds 1 + p + ... + p^k, so x is a product of such sums for rising primes: each
  // is a divisor of x, and the search runs over the divisors of x alone, the primes that make
  // a divisor such a sum worked out the first time the search meets it.
  //
  // A step of the search walks the divisors of the part of x it has left, made from that part's
  // own factors - all among those of x - and tries that part as one sum first. It walked every
  // divisor of x up to that part, and asked each whether it divided the part: a value of 19
  // digits has up to 161,280 divisors and the search some ten thousand steps, so membership of
  // 8415117044874540000 took four seconds, and a flag over four such values ran into the time a
  // run has after the first.
  //
  // Not the type's build: pqBuild() ranks function.php before bool.php.

  include_once PQ . '_lib/factors.php';

  function pqBoolSigma ( $x, $p=0 ) {

    if ( ! pqBoolWhole ( $x ) or $x < 1 )
      return FALSE;

    $x = (int) $x;

    $sums = [];

    return pqSigmaHas ( $x, 2, array_keys ( pqFactors ( $x ) ), $sums );

  }

  // Whether $m is the sigma of a number whose primes are all $least or more. $primes are those
  // of the value asked about, which hold every prime of $m; $sums keeps the primes of each
  // divisor once worked out. A sum that is $least or less has no prime $least or more.

  function pqSigmaHas ( $m, $least, $primes, &$sums ) {

    if ( $m == 1 )
      return TRUE;

    foreach ( $sums [$m] ??= pqSigmaPrimes ( $m ) as $prime )
      if ( $prime >= $least )
        return TRUE;

    $factors = [];

    foreach ( $primes as $factor )
      for ( $rest = $m; $rest % $factor == 0; $rest = intdiv ( $rest, $factor ) )
        $factors [$factor] = ( $factors [$factor] ?? 0 ) + 1;

    foreach ( pqDivisorList ( $m, $factors ) as $d ) {

      if ( $d <= $least or $d == $m )
        continue;

      foreach ( $sums [$d] ??= pqSigmaPrimes ( $d ) as $prime )
        if ( $prime >= $least and pqSigmaHas ( intdiv ( $m, $d ), $prime + 1, $primes, $sums ) )
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
