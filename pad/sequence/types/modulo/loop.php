<?php

  // Build strategy 'loop' for the modulo sequence: each term is the loop value modulo the
  // parameter, which defaults to 1. {modulo 3} over 1, 2, 3, ... gives 1, 2, 0, 1, 2, 0,
  // ..., which makes it a handy cycling counter as well as an arithmetic play.

  // fmod rather than %, so the parameter is used as it was written rather than cut down to a
  // whole number - cutting it down turned any parameter below 1 into a modulo of zero. A
  // whole parameter still gives whole answers.

  // A value that is no number has no term here - a word in the list a make play is handed
  // ended the request on the arithmetic.

  if ( ! is_numeric ( $pqLoop ) )
    return FALSE;

  // A parameter that is 0 - spelled 0, '0.0', '-0' or '00' - is read as 1. ! $pqParm let the
  // strings '0.0', '-0' and '00' through, which are true, and {sequence modulo='0.0'} ended the
  // request on "Modulo by zero" where modulo=0 gives 0 0 0.

  if ( is_numeric ( $pqParm ) ? $pqParm == 0 : ! $pqParm )
    $pqParm = 1;

  // pqZero turns the float -0 that rounds a small negative value, and that fmod gives for a
  // negative multiple, into 0: printed, it read -0.

  // In whole numbers when both are whole - % keeps the sign of the dividend, as fmod does -
  // where fmod lost the last digits past 2^53.
  if ( pqBoolWhole ( $pqLoop ) and pqBoolWhole ( $pqParm ) and $pqParm !== TRUE )
    return (int) $pqLoop % (int) $pqParm;
  return pqZero ( fmod ( $pqLoop, $pqParm ) );

?>
