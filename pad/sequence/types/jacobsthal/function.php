<?php

  // Function build for jacobsthal: pqJacobsthal($n) is the nth Jacobsthal number, J(0) = 0,
  // J(1) = 1 and J(n) = J(n-1) + 2J(n-2) - 1, 1, 3, 5, 11, 21, 43, 85, ... from n = 1, 0 at
  // position 0. Walked as J(n) = 2J(n-1) + 1 for an odd n and - 1 for an even one, which is
  // the same sequence and never leaves the integer range while the term is inside it; J(64)
  // is the last there, and the float after it ends the build. A position below 0, or between
  // two, has no term.

  function pqJacobsthal ( $n ) {

    if ( ! pqBoolWhole ( $n ) or $n < 0 )
      return FALSE;

    for ( $term = 0, $i = 1; $i <= $n; $i++ ) {

      $term = 2 * $term + ( $i % 2 ? 1 : -1 );

      if ( is_float ( $term ) )
        return $term;

    }

    return $term;

  }

?>
