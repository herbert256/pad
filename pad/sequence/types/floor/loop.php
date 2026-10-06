<?php

  // Build strategy 'loop' for the floor sequence: each term is the loop value rounded down
  // to a multiple of the parameter, which defaults to 1. {floor 5} over 1..10 gives 0, 0,
  // 0, 0, 5, 5, 5, 5, 5, 10. The counterpart of ceil, and likewise mostly used as a play.

  // A value that is no number has no term here - a word in the list a make play is handed
  // ended the request on the arithmetic.

  if ( ! is_numeric ( $pqLoop ) )
    return FALSE;

  if ( ! $pqParm )
    $pqParm = 1;

  return floor ( $pqLoop / $pqParm ) * $pqParm;

?>
