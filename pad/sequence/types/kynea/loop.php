<?php

  // Build strategy 'loop' for the kynea sequence: each term is the Kynea number
  // (2^n + 1)^2 - 2, built in three steps from a left shift - 7, 23, 79, 287, 1087, 4223,
  // ... Exact terms run out at n = 31, which is where generated.php stops.
  //
  // Past n = 62 the shift has no answer - 1 << 64 is 0, and n = 64 on gave the term -1 for
  // every position - so those answer a float out of the integer range at once, which ends
  // the build, and below position 0 there is no term: a negative shift ended the request on
  // "Bit shift by negative number".
  //
  // It works in $pqLoop itself rather than a local, which is safe only because both
  // iterators reassign the candidate before every call.

  if ( ! pqBoolWhole ( $pqLoop ) or $pqLoop < 0 )
    return FALSE;

  if ( $pqLoop > 62 )
    return (float) PHP_INT_MAX * 2;

    $pqLoop = (1 << $pqLoop) + 1;
    $pqLoop = $pqLoop * $pqLoop;
    $pqLoop = $pqLoop - 2;

    return $pqLoop;

?>
