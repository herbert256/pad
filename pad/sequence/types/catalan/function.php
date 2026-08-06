<?php

  // Build strategy 'function' for the catalan sequence: pqCatalan($n) returns the nth
  // Catalan number C(2n,n)/(n+1), which counts things like the bracketings of n pairs of
  // parentheses. Counting from $pqLoop = 1 the terms are 1, 2, 5, 14, 42, 132, ... - the
  // leading C(0) = 1 of the usual listing is never produced.
  //
  // The terms are stepped up one at a time through C(i) = C(i-1) * 2 * (2i-1) / (i+1), in
  // whole numbers throughout. The step is reduced to its lowest terms first, which leaves a
  // denominator dividing the running value exactly, so intdiv() loses nothing and the
  // largest number formed is the term itself. C(2n,n) is deliberately never built: it passes
  // PHP_INT_MAX while the Catalan number still fits comfortably, and computing it in
  // floating point costs the last digits - that is what made C(28) onwards come out short.
  //
  // C(35) is the last term inside 64-bit range. From C(36) the multiplication overflows to a
  // float, which is handed back as it stands so build/one.php can end the build on it.

  function pqCatalanGcd ( $a, $b ) {

    while ( $b ) {
      $pqSwap = $b;
      $b      = $a % $b;
      $a      = $pqSwap;
    }

    return $a;

  }

  function pqCatalan ( $n ) {

    $c = 1;

    for ( $i = 1; $i <= $n; $i++ ) {

      if ( ! is_int ( $c ) )
        return $c;

      $num = 2 * ( 2 * $i - 1 );
      $den = $i + 1;

      $gcd = pqCatalanGcd ( $num, $den );
      $num = intdiv ( $num, $gcd );
      $den = intdiv ( $den, $gcd );

      $c = intdiv ( $c, $den ) * $num;

    }

    return $c;

  }

?>