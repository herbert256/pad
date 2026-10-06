<?php

  // Membership predicate for prime: pqBoolPrime($n) via gmp_prob_prime(). Used for {keep
  // prime} and friends, and by prime/loop.php, which is how the type generates.
  //
  // gmp_prob_prime() is the fast path and needs the gmp extension; without it the same
  // question is answered by trial division against the odd numbers up to sqrt(n).
  //
  // A prime is 2 or more on both paths: gmp tests the absolute value, so with the extension
  // -7, -5, -3 and -2 were primes and {sequence '-10..10', keep='prime'} kept them, where
  // the trial division - and every other membership test - turned them down.

  function pqBoolPrime ( $n, $p=0 ) {

    if ( ! pqBoolWhole ( $n ) or $n < 2 )
      return FALSE;

    $n = (int) $n;

    if ( ! function_exists ( 'gmp_prob_prime' ) ) {

      if ( $n < 4       ) return TRUE;
      if ( $n % 2 == 0  ) return FALSE;

      for ( $pqPrimeTry = 3; $pqPrimeTry * $pqPrimeTry <= $n; $pqPrimeTry += 2 )
        if ( $n % $pqPrimeTry == 0 )
          return FALSE;

      return TRUE;

    }

    if ( gmp_prob_prime ($n) )
      return TRUE;
    else
      return FALSE;

  }

?>
