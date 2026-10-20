<?php

  // Build strategy 'loop' for the cubic sequence: each term is the cube of the loop value -
  // 1, 8, 27, 64, 125, 216, ...
  //
  // The term is the formula of the position in function.php, pqCubic(), which also answers
  // build='function' and sequence:cubic(n) past a walk's reach. A value that is no whole number of
  // 0 or more - a word or a fraction in the list a make play is handed, a position below the
  // first - has no term there.

  include_once PT . 'cubic/function.php';

  return pqCubic ( $pqLoop );

?>
