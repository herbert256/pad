<?php

  // Build strategy 'loop' for the biquadratic sequence: each term is the fourth power of
  // the loop value - 1, 16, 81, 256, 625, 1296, ...
  //
  // The term is the formula of the position in function.php, pqBiquadratic(), which also answers
  // build='function' and sequence:biquadratic(n) past a walk's reach. A value that is no whole number of
  // 0 or more - a word or a fraction in the list a make play is handed, a position below the
  // first - has no term there.

  include_once PT . 'biquadratic/function.php';

  return pqBiquadratic ( $pqLoop );

?>
