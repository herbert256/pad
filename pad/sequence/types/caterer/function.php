<?php

  // Build strategy 'function' for the caterer sequence - the lazy caterer's numbers,
  // n(n+1)/2 + 1, the most pieces a pancake can be cut into with n straight cuts. Counting
  // from $pqLoop = 1 the terms are 2, 4, 7, 11, 16, 22, 29, ...

function pqCaterer ($n)
{

    return ($n * ( $n + 1)) / 2 + 1;
}

?>