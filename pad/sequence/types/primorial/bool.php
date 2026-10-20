<?php

  // Membership test for primorial, 1, 2, 6, 30, 210, ...: the primes are divided out of x in
  // order, each once, and 1 must be left before a prime does not divide it. build/check.php
  // asks it for {keep primorial}, {remove primorial} and {flag primorial}.
  //
  // Not the type's build: pqBuild() ranks function.php before bool.php.

  function pqBoolPrimorial ( $x, $p=0 ) {

    if ( ! pqBoolWhole ( $x ) or $x < 1 )
      return FALSE;

    $x = (int) $x;

    foreach ( [ 2, 3, 5, 7, 11, 13, 17, 19, 23, 29, 31, 37, 41, 43, 47 ] as $prime ) {

      if ( $x == 1 )
        return TRUE;

      if ( $x % $prime )
        return FALSE;

      $x = intdiv ( $x, $prime );

    }

    return $x == 1;

  }

?>
