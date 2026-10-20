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

  // The test is bool.php's: a reversal past the integer range is no prime the engine can name,
  // where the (int) of it ended the request on "not representable as an int" - {sequence
  // emirp, from=1000000000000000070}.

  include_once PT . 'emirp/bool.php';

  return pqBoolEmirp ( $pqLoop );

?>
