<?php

  // Membership test for the exponentiation sequence, n ** e for n from 1 up - 1, 8, 27 for
  // exponentiation=3. build/check.php asks it for {keep exponentiation}, {remove
  // exponentiation} and {flag exponentiation}; without it the check looked for x among the
  // first x terms, where an exponent below 1 puts the terms further out - 2 is the fourth
  // term of exponentiation=0.5 - and answered no.
  //
  // The n that could give x is its e-th root, so the whole n next to the root are tried with
  // the same ** loop.php uses. Every term is above 0, an exponent of 0 makes only 1, and a
  // parameter of nothing is the 1 a bare option reads as.

  function pqBoolExponentiation ( $x, $e=0 ) {

    if ( ! is_numeric ( $x ) or $x <= 0 )
      return FALSE;

    $e = ( $e === TRUE ) ? 1 : $e;

    if ( ! is_numeric ( $e ) )
      return FALSE;

    if ( $e == 0 )
      return ( $x == 1 );

    // With e = 1 every whole x is its own n - asked through the root, an x past 2^53 came back
    // from the float x ** 1.0 rounded, and the window of three missed it.

    if ( $e == 1 )
      return pqBoolWhole ( $x );

    // The root is a float, and one past the integer range was cast with (int): x = 4000000000
    // for e = 0.5 has the root 1.6e19, and the request ended on "The float ... is not
    // representable as an int". A position is an integer, so a root past the range stands
    // for the last ones there, and one past what a float holds - the root of 10 for
    // exponentiation=0.001 is 10^1000 - for none.

    $root = $x ** ( 1 / $e );

    if ( ! is_finite ( $root ) )
      return FALSE;

    $root = ( $root >= PHP_INT_MAX ) ? PHP_INT_MAX : (int) round ( $root );

    // A power past the integer range is no term - a run ends at it - though as a float it
    // compares equal to PHP_INT_MAX: 2097152 ** 3 is 2^63, and PHP_INT_MAX read as a cube.

    foreach ( [ $root - 1, $root, $root + 1 ] as $n ) {

      if ( ! is_int ( $n ) or $n < 1 )
        continue;

      $power = $n ** $e;

      if ( is_float ( $power ) and $power >= PHP_INT_MAX )
        continue;

      if ( $power == $x )
        return TRUE;

    }

    return FALSE;

  }

?>
