<?php

  // Loop build for square: the loop value squared, so 1, 4, 9, 16, 25, 36, ...
  //
  // The term is the formula of the position in function.php, pqSquare(), which also answers
  // build='function' and sequence:square(n) past a walk's reach. A value that is no whole number of
  // 0 or more - a word or a fraction in the list a make play is handed, a position below the
  // first - has no term there.

  include_once PT . 'square/function.php';

  return pqSquare ( $pqLoop );

?>
