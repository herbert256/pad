<?php

  // Membership predicate and generation path for safePrime: pqBoolSafePrime($n) is TRUE for a
  // prime n for which (n - 1) / 2 is a prime as well - 5, 7, 11, 23, 47, 59, 83, 107, 167,
  // 179, ... - the (n - 1) / 2 being the Sophie Germain primes. Primality goes to
  // prime/bool.php. The type's only build, so the whole range is filtered through it.

  include_once PT . 'prime/bool.php';

  function pqBoolSafePrime ( $n, $p=0 ) {

    if ( ! pqBoolPrime ( $n ) or $n < 5 )
      return FALSE;

    return pqBoolPrime ( intdiv ( (int) $n - 1, 2 ) );

  }

?>
