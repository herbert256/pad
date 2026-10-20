<?php

  // Membership test for totient: pqBoolTotient($x) is TRUE when x is the totient of some n -
  // 1, 2, 4, 6, 8, 10, 12, 16, 18, 20, ... An odd value above 1 never is. For an even one the
  // n is put together from prime powers: p^k takes p^(k-1) * (p - 1) of x, so p - 1 is a
  // divisor of x, and what is left must be the totient of a number of larger primes. The
  // search runs over the divisors d of x with d + 1 a prime, found once. Looking among the
  // terms of the first positions could not answer it: the n of a totient can stand far past
  // the value itself.
  //
  // Not the type's build: pqBuild() ranks function.php before bool.php.

  include_once PQ . '_lib/factors.php';

  function pqBoolTotient ( $x, $p=0 ) {

    if ( ! pqBoolWhole ( $x ) or $x < 1 )
      return FALSE;

    $x = (int) $x;

    if ( $x == 1 )
      return TRUE;

    if ( $x % 2 )
      return FALSE;

    $steps = array_filter ( pqDivisorList ( $x ), fn ( $d ) => pqBoolPrime ( $d + 1 ) );

    return pqTotientHas ( $x, 2, $steps );

  }

  // Whether $m is the totient of a number whose primes are all $least or more. $steps are the
  // divisors of the value asked about that are one less than a prime, rising.

  function pqTotientHas ( $m, $least, $steps ) {

    if ( $m == 1 )
      return TRUE;

    foreach ( $steps as $d ) {

      if ( $d > $m )
        break;

      if ( $d + 1 < $least or $m % $d )
        continue;

      for ( $prime = $d + 1, $rest = intdiv ( $m, $d ); ; $rest = intdiv ( $rest, $prime ) ) {

        if ( pqTotientHas ( $rest, $prime + 1, $steps ) )
          return TRUE;

        if ( $rest % $prime )
          break;

      }

    }

    return FALSE;

  }

?>
