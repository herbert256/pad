<?php

  // Membership test for the kynea sequence, 7, 23, 79, 287, ...: (2^n + 1)^2 - 2 for a whole
  // n from 1 up. The terms about quadruple at every step, so the n that could make x are
  // walked until a term passes it - 31 of them at the most, the last term inside the integer
  // range. build/check.php asks it for {keep kynea}, {remove kynea} and {flag kynea}.
  //
  // Not the type's build: pqBuild() ranks loop.php before bool.php.

  function pqBoolKynea ( $x, $p=0 ) {

    if ( ! pqBoolWhole ( $x ) or $x < 7 )
      return FALSE;

    $x = (int) $x;

    for ( $n = 1; $n <= 31; $n++ ) {

      $term = ( ( 1 << $n ) + 1 ) ** 2 - 2;

      if ( $term == $x )
        return TRUE;

      if ( $term > $x )
        return FALSE;

    }

    return FALSE;

  }

?>
