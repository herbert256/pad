<?php

  // Build strategy 'loop' for the cullen sequence: each term is the Cullen number
  // n * 2^n + 1 - 3, 9, 25, 65, 161, 385, 897, ... Exact terms run out at n = 57, which is
  // where generated.php stops.
  //
  // The term is the formula of the position in function.php, pqCullen(), which also answers
  // build='function' and sequence:cullen(n) past a walk's reach. A value that is no whole number of
  // 0 or more - a word or a fraction in the list a make play is handed, a position below the
  // first - has no term there.

  include_once PT . 'cullen/function.php';

  return pqCullen ( $pqLoop );

?>
