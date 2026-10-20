<?php

  // Membership predicate and generation path for sophieGermain: pqBoolSophieGermain($n) is TRUE
  // for a prime n for which 2n + 1 is a prime as well - 2, 3, 5, 11, 23, 29, 41, 53, 83, 89,
  // ... - the 2n + 1 being the safe primes. Primality goes to prime/bool.php; a 2n + 1 past
  // the integer range is asked of gmp when it is there, and is out of reach when it is not.
  // The type's only build, so the whole range is filtered through it.

  include_once PT . 'prime/bool.php';

  function pqBoolSophieGermain ( $n, $p=0 ) {

    if ( ! pqBoolPrime ( $n ) )
      return FALSE;

    $n = (int) $n;

    if ( $n <= intdiv ( PHP_INT_MAX - 1, 2 ) )
      return pqBoolPrime ( 2 * $n + 1 );

    return function_exists ( 'gmp_prob_prime' ) and gmp_prob_prime ( gmp_add ( gmp_mul ( $n, 2 ), 1 ) ) > 0;

  }

?>
