<?php

  // Build strategy 'function' for the caterer sequence - the lazy caterer's numbers,
  // n(n+1)/2 + 1, the most pieces a pancake can be cut into with n straight cuts. Counting
  // from $pqLoop = 1 the terms are 2, 4, 7, 11, 16, 22, 29, ...

function pqCaterer ($n)
{

    // No cut, n = 0, leaves the pancake whole; a position below it, or between two, has no
    // term - the formula answered 1 at -1 and 2 at -2.

    if ( ! pqBoolWhole ( $n ) or $n < 0 )
      return FALSE;

    return pqProduct ( [ $n, $n + 1 ], 2 ) + 1;
}

?>
