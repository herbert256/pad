<?php

  // Build strategy 'loop' for the icosihenagonal sequence: each term is the nth twenty-one
  // sided figurate number, n(19n - 17)/2 - 1, 21, 60, 118, 195, 291, ...
  //
  // The term is the formula of the position in function.php, pqIcosihenagonal(), which also answers
  // build='function' and sequence:icosihenagonal(n) past a walk's reach. A value that is no whole number of
  // 0 or more - a word or a fraction in the list a make play is handed, a position below the
  // first - has no term there.

  include_once PT . 'icosihenagonal/function.php';

  return pqIcosihenagonal ( $pqLoop );

?>
