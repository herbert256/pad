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

    $root = (int) round ( $x ** ( 1 / $e ) );

    for ( $n = max ( 1, $root - 1 ); $n <= $root + 1; $n++ )
      if ( $n ** $e == $x )
        return TRUE;

    return FALSE;

  }

?>
