<?php

  // Loop build for square: the loop value squared, so 1, 4, 9, 16, 25, 36, ...

  // A value that is no number has no term here - a word in the list a make play is handed
  // ended the request on the arithmetic.

  // A position below the first one, 0, or between two, has no term, as for every type that
  // counts its terms: square at -2 gave 4 and cubic -8, which its own test denied.

  if ( ! pqBoolWhole ( $pqLoop ) or $pqLoop < 0 )
    return FALSE;

  return $pqLoop * $pqLoop;

?>
