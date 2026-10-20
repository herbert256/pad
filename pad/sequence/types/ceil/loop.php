<?php

  // Build strategy 'loop' for the ceil sequence: each term is the loop value rounded up to
  // the next multiple of the parameter, which defaults to 1. {ceil 5} over 1..10 gives
  // 5, 5, 5, 5, 5, 10, 10, 10, 10, 10. Mostly used as a play, where {make ceil=5} rounds
  // another sequence's terms up to a multiple of five.

  // The parameter is used as it was written rather than cut down to a whole number, so a
  // fractional one rounds to fractional multiples the way floor and round already did.
  // Cutting it down turned any parameter below 1 into a divisor of zero.

  // A value that is no number has no term here - a word in the list a make play is handed
  // ended the request on the arithmetic.

  if ( ! is_numeric ( $pqLoop ) )
    return FALSE;

  // A parameter that is 0 - spelled 0, '0.0', '-0' or '00' - is read as 1. ! $pqParm let the
  // strings '0.0', '-0' and '00' through, which are true, and {sequence ceil='0.0'} ended the
  // request on "Division by zero" where ceil=0 gives 1 2 3.

  if ( is_numeric ( $pqParm ) ? $pqParm == 0 : ! $pqParm )
    $pqParm = 1;

  // pqZero turns the float -0 that rounds a small negative value, and that fmod gives for a
  // negative multiple, into 0: printed, it read -0.

  // A multiple of -2 is one of 2: divided by the signed parameter, ceil=-2 rounded the other
  // way - 1 to 0, as floor=2 does.

  $pqCeilBy = abs ( $pqParm );

  // In whole numbers when both are whole, pqCeilMultiple.
  return pqZero ( pqCeilMultiple ( $pqLoop, $pqCeilBy ) );

?>
