<?php

  // The arithmetic of the divisor types - totient, divisors, sigma - and of the number
  // properties built on the factors of a number: abundant, deficient, squarefree, smith,
  // sphenic, carmichael and amicable. Those types include it, so a request that uses none of
  // them compiles none of it.
  //
  // pqFactors      the prime factors of a whole number from 1 up, as [ prime => exponent ]
  // pqDivisorList  every divisor of a whole number, rising
  // pqSigmaOf      the sum of the divisors of a whole number - past the integer range the
  //                float it overflows to

  include_once PT . 'prime/bool.php';

  // The small factors come by trial division - 2, 3, 5 and the numbers after them that are
  // no multiple of those - and what is left above 10,000 is split by Pollard's rho through
  // gmp, so a number of 19 digits is factored in well under a second where trial division to
  // its square root takes a billion steps. Without gmp the trial division goes on to the root.

  function pqFactors ( $n ) {

    $n       = (int) $n;
    $factors = [];

    if ( $n < 2 )
      return $factors;

    foreach ( [ 2, 3, 5 ] as $p )
      while ( $n % $p == 0 ) {
        $factors [$p] = ( $factors [$p] ?? 0 ) + 1;
        $n = intdiv ( $n, $p );
      }

    $gaps = [ 4, 2, 4, 2, 4, 6, 2, 6 ];
    $most = function_exists ( 'gmp_prob_prime' ) ? 10000 : PHP_INT_MAX;

    for ( $p = 7, $i = 0; $p <= $most and $p <= intdiv ( $n, $p ); $p += $gaps [ $i++ % 8 ] )
      while ( $n % $p == 0 ) {
        $factors [$p] = ( $factors [$p] ?? 0 ) + 1;
        $n = intdiv ( $n, $p );
      }

    if ( $n > 1 )
      pqFactorsRest ( $n, $factors );

    ksort ( $factors );

    return $factors;

  }

  // The factors of what trial division left: a prime as it is, else split in two by Pollard's
  // rho and both parts factored on.

  function pqFactorsRest ( $n, &$factors ) {

    if ( ! function_exists ( 'gmp_prob_prime' ) or pqBoolPrime ( $n ) ) {
      $factors [$n] = ( $factors [$n] ?? 0 ) + 1;
      return;
    }

    $big = gmp_init ( $n );

    for ( $c = 1; ; $c++ ) {

      $x = $y = gmp_init ( 2 );
      $d = gmp_init ( 1 );

      while ( gmp_cmp ( $d, 1 ) == 0 ) {
        $x = gmp_mod ( gmp_add ( gmp_mul ( $x, $x ), $c ), $big );
        $y = gmp_mod ( gmp_add ( gmp_mul ( $y, $y ), $c ), $big );
        $y = gmp_mod ( gmp_add ( gmp_mul ( $y, $y ), $c ), $big );
        $d = gmp_gcd ( gmp_sub ( $x, $y ), $big );
      }

      if ( gmp_cmp ( $d, $big ) != 0 ) {
        $d = gmp_intval ( $d );
        pqFactorsRest ( $d, $factors );
        pqFactorsRest ( intdiv ( $n, $d ), $factors );
        return;
      }

    }

  }

  function pqDivisorList ( $n ) {

    $divisors = [ 1 ];

    foreach ( pqFactors ( $n ) as $p => $e ) {

      $more = [];

      foreach ( $divisors as $d )
        for ( $i = 1, $power = $d; $i <= $e; $i++ )
          $more [] = $power *= $p;

      $divisors = array_merge ( $divisors, $more );

    }

    sort ( $divisors );

    return $divisors;

  }

  function pqSigmaOf ( $n ) {

    $sigma = 1;

    foreach ( pqFactors ( $n ) as $p => $e ) {

      for ( $sum = 1, $power = 1, $i = 1; $i <= $e; $i++ )
        $sum += $power *= $p;

      $sigma *= $sum;

    }

    return $sigma;

  }

?>
