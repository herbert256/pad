<?php

  // Build strategy 'loop' for the biquadratic sequence: each term is the fourth power of
  // the loop value - 1, 16, 81, 256, 625, 1296, ...

  // A value that is no number has no term here - a word in the list a make play is handed
  // ended the request on the arithmetic.

  // A position below the first one, 0, or between two, has no term, as for every type that
  // counts its terms: square at -2 gave 4 and cubic -8, which its own test denied.

  if ( ! pqBoolWhole ( $pqLoop ) or $pqLoop < 0 )
    return FALSE;

return $pqLoop ** 4;

?>
