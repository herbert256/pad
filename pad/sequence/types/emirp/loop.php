<?php

  // Build strategy 'loop' for the emirp sequence: keeps the loop value when both it and its
  // digit reversal are prime, testing with gmp_prob_prime() and reversing with
  // padTypeReverse().
  //
  // Two things worth knowing. The primality test needs the gmp extension, and without it
  // every candidate is accepted, so the sequence quietly degenerates into the plain range
  // instead of failing. And the reversal is never required to differ from the number
  // itself, so palindromic primes from 11 upwards are let through: the terms are 11, 13,
  // 17, 31, 37, 71, 73, 79, 97, 101, ..., not the strict emirps, which exclude 11 and 101.

  if ( ! function_exists ( 'gmp_prob_prime' ) )
    return TRUE;

  if ( ! gmp_prob_prime ( $pqLoop ) )
    return false;

  if ( $pqLoop < 11 )
    return false;

  $padReverse = (int) padTypeReverse($pqLoop);

  if ( gmp_prob_prime ( $padReverse ) )
    return TRUE;
  else
    return false;

?>