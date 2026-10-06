<?php

  // Membership predicate for powerful: pqBoolPowerful($n) is TRUE when every prime dividing
  // n divides it at least twice - the powerful numbers 1, 4, 8, 9, 16, 25, 27, 32, 36, 49,
  // 64, 72, ...
  //
  // The prime factors are divided out while their cube stays within what is left, rejecting
  // as soon as an exponent comes out 1. Every prime factor of what is left then lies above
  // its cube root, so it holds at most two of them: it is 1, a prime, a product of two primes
  // or the square of one, and only 1 and the square are powerful. Dividing on up to the
  // square root cost a billion divisions for the product of two large primes. The only file
  // in the type, so it is the generation path as well and the whole range is filtered
  // through it.
  //
  // Dividing 2 out of 0 leaves 0, so the loop would never end; the powerful numbers are
  // positive, so anything under 1 is answered before it.

  function pqBoolPowerful ($n, $p=0) {

    if ( ! pqBoolWhole ( $n ) or $n < 1 )
      return FALSE;

    $n = (int) $n;

    for ( $factor = 2; $factor * $factor * $factor <= $n; $factor++ ) {

      if ( $n % $factor )
        continue;

      $power = 0;

      while ( $n % $factor == 0 ) {
        $n = intdiv ( $n, $factor );
        $power++;
      }

      if ( $power == 1 )
        return FALSE;

    }

    $root = (int) sqrt ( (float) $n );

    for ( $i = max ( 1, $root - 1 ); $i <= $root + 1; $i++ )
      if ( $i * $i == $n )
        return TRUE;

    return FALSE;

  }

?>
