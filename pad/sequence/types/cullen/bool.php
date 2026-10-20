<?php

  // Membership test for the cullen sequence, 1, 3, 9, 25, 65, ...: n * 2^n + 1 for a whole
  // n from 0 up. The terms more than double at every step, so the n that could make x are
  // walked until a term passes it - 62 of them at the most. build/check.php asks it for {keep
  // cullen}, {remove cullen} and {flag cullen}.
  //
  // Not the type's build: pqBuild() ranks loop.php before bool.php.

  function pqBoolCullen ( $x, $p=0 ) {

    // From n = 0, whose term 1 the type makes at position 0.

    if ( ! pqBoolWhole ( $x ) or $x < 1 )
      return FALSE;

    $x = (int) $x;

    for ( $n = 0; $n <= 57; $n++ ) {

      $term = ( 1 << $n ) * $n + 1;

      if ( $term == $x )
        return TRUE;

      if ( $term > $x )
        return FALSE;

    }

    return FALSE;

  }

?>
