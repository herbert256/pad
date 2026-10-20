<?php

  // Build strategy 'loop' for the floor sequence: each term is the loop value rounded down
  // to a multiple of the parameter, which defaults to 1. {floor 5} over 1..10 gives 0, 0,
  // 0, 0, 5, 5, 5, 5, 5, 10. The counterpart of ceil, and likewise mostly used as a play.

  // A value that is no number has no term here - a word in the list a make play is handed
  // ended the request on the arithmetic.

  if ( ! is_numeric ( $pqLoop ) )
    return FALSE;

  // A parameter that is 0 - spelled 0, '0.0', '-0' or '00' - is read as 1. ! $pqParm let the
  // strings '0.0', '-0' and '00' through, which are true, and {sequence floor='-0'} ended the
  // request on "Division by zero" where floor=0 gives 1 2 3.

  if ( is_numeric ( $pqParm ) ? $pqParm == 0 : ! $pqParm )
    $pqParm = 1;

  // pqZero turns the float -0 into 0: printed, it read -0. It comes of a float 0 - a listed
  // 0.0, or a value that rounds to 0 - and a negative sign: floor ( -0.0 / 3 ) * 3.

  // A multiple of -2 is one of 2: divided by the signed parameter, floor=-2 rounded the other
  // way - 1 to 2, as ceil=2 does.

  $pqFloorBy = abs ( $pqParm );

  // In whole numbers when both are whole, pqFloorMultiple.
  return pqZero ( pqFloorMultiple ( $pqLoop, $pqFloorBy ) );

?>
