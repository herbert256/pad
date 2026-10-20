<?php

  // Membership test for woodall, 1, 7, 23, 63, 159, ...: the terms more than double at every
  // step, so the n that could make x are walked until a term passes it - 57 of them at the
  // most. build/check.php asks it for {keep woodall}, {remove woodall} and {flag woodall}.
  //
  // Not the type's build: pqBuild() ranks function.php before bool.php.

  function pqBoolWoodall ( $x, $p=0 ) {

    if ( ! pqBoolWhole ( $x ) or $x < 1 )
      return FALSE;

    $x = (int) $x;

    for ( $n = 1; $n <= 57; $n++ ) {

      $term = ( 1 << $n ) * $n - 1;

      if ( $term >= $x )
        return $term == $x;

    }

    return FALSE;

  }

?>
