<?php

  // Membership predicate for prime: pqBoolPrime($n) via gmp_prob_prime(). Used for {keep
  // prime} and friends, and by prime/loop.php, which is how the type generates.
  //
  // gmp_prob_prime() is the fast path and needs the gmp extension; without it the same
  // question is answered by trial division against the odd numbers up to sqrt(n).

  function pqBoolPrime ( $n, $p=0 ) {

    if ( ! function_exists ( 'gmp_prob_prime' ) ) {

      if ( $n < 2       ) return FALSE;
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