<?php

  // Function build for pentatope: pqPentatope($n) is the nth pentatope number, n(n+1)(n+2)(n+3)
  // / 24 - 1, 5, 15, 35, 70, 126, 210, ... from n = 1, 0 at position 0 - the fourth diagonal of
  // Pascal's triangle. Made as C(n+1, 2), C(n+2, 3) and C(n+3, 4) in turn, each a whole
  // number, the last division done before the last multiplication so the product never leaves
  // the integer range while the term is inside it. Past that the float it overflows to ends
  // the build. A position below 0, or between two, has no term.

  function pqPentatope ( $n ) {

    if ( ! pqBoolWhole ( $n ) or $n < 0 )
      return FALSE;

    $n = (int) $n;

    if ( $n > 200000 )
      return (float) PHP_INT_MAX * 2;

    $term  = intdiv ( $n * ( $n + 1 ), 2 );
    $term  = intdiv ( $term * ( $n + 2 ), 3 );
    $last  = $n + 3;
    $share = $last % 4 == 0 ? 4 : ( $last % 2 == 0 ? 2 : 1 );

    return intdiv ( $term, intdiv ( 4, $share ) ) * intdiv ( $last, $share );

  }

?>
