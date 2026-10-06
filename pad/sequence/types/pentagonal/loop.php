<?php

  // Loop build for pentagonal: (3n^2 - n)/2, the nth pentagonal number, so 1, 5, 12, 22,
  // 35, 51, 70, ...

  // A position below the first one, or between two, has no term: the formula answered there
  // with numbers that are no terms of the sequence - pentagonal at -2 and -1 gave 7 and 2.

  if ( ! pqBoolWhole ( $pqLoop ) or $pqLoop < 0 )
    return FALSE;

  return pqProduct ( [ $pqLoop, 3 * $pqLoop - 1 ], 2 );

?>
