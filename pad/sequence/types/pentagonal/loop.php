<?php

  // Loop build for pentagonal: (3n^2 - n)/2, the nth pentagonal number, so 1, 5, 12, 22,
  // 35, 51, 70, ...
  //
  // The term is the formula of the position in function.php, pqPentagonal(), which also answers
  // build='function' and sequence:pentagonal(n) past a walk's reach. A value that is no whole number of
  // 0 or more - a word or a fraction in the list a make play is handed, a position below the
  // first - has no term there.

  include_once PT . 'pentagonal/function.php';

  return pqPentagonal ( $pqLoop );

?>
