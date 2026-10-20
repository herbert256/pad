<?php

  // Membership predicate and generation path for twinPrime: pqBoolTwinPrime($n) is TRUE for a
  // prime n with a prime two below or two above it - 3, 5, 7, 11, 13, 17, 19, 29, 31, 41, 43,
  // ... - both of every pair. Primality goes to prime/bool.php. The type's only build, so the
  // whole range is filtered through it.

  include_once PT . 'prime/bool.php';

  function pqBoolTwinPrime ( $n, $p=0 ) {

    if ( ! pqBoolPrime ( $n ) or $n < 3 )
      return FALSE;

    $n = (int) $n;

    return pqBoolPrime ( $n - 2 ) or pqBoolPrime ( $n + 2 );

  }

?>
