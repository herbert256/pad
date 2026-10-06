<?php

  // Function build for tetrahedral: pqTetrahedral($n) = n(n+1)(n+2)/6, the running total of
  // the triangular numbers and so the number of spheres in a triangular pyramid of n
  // layers - 1, 4, 10, 20, 35, 56, 84, ...

function pqTetrahedral ($n) {

  // A position below the first one, or between two, has no term: the formula answered there
  // with numbers that are no terms of the sequence - pentagonal at -2 and -1 gave 7 and 2.

  if ( ! pqBoolWhole ( $n ) or $n < 0 )
    return FALSE;


  return pqProduct ( [ $n, $n + 1, $n + 2 ], 6 );

}

?>
