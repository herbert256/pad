<?php

  // Build strategy 'loop' for the kynea sequence: each term is the Kynea number
  // (2^n + 1)^2 - 2 - 7, 23, 79, 287, 1087, 4223, ... Exact terms run out at n = 31, which is
  // where generated.php stops.
  //
  // The term is the formula of the position in function.php, pqKynea(), which also answers
  // build='function' and sequence:kynea(n) past a walk's reach. A value that is no whole number of
  // 0 or more - a word or a fraction in the list a make play is handed, a position below the
  // first - has no term there.

  include_once PT . 'kynea/function.php';

  return pqKynea ( $pqLoop );

?>
