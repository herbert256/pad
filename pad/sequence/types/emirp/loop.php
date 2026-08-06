<?php

  // Build strategy 'loop' for the emirp sequence: keeps the loop value when it is prime, its
  // digit reversal is prime, and the two differ. The terms are 13, 17, 31, 37, 71, 73, 79,
  // 97, 107, 113, 149, 157, ...
  //
  // The reversal has to be a different prime, which is what separates an emirp from a
  // palindromic prime: 11, 101, 131 and 151 read the same backwards, so they reverse into a
  // prime only by being one. That comparison is the whole difference between this sequence
  // and the primes whose reversal happens to be prime.
  //
  // Primality goes through prime/bool.php rather than gmp_prob_prime() directly, so the
  // trial-division fallback that type carries applies here too - without it a missing gmp
  // extension let every candidate through and the sequence quietly became the plain range.

  include_once PT . 'prime/bool.php';

  if ( ! pqBoolPrime ( $pqLoop ) )
    return FALSE;

  $padReverse = (int) padTypeReverse ( $pqLoop );

  if ( $padReverse == $pqLoop )
    return FALSE;

  if ( ! pqBoolPrime ( $padReverse ) )
    return FALSE;

  return TRUE;

?>