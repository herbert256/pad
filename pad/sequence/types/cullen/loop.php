<?php

  // Build strategy 'loop' for the cullen sequence: each term is the Cullen number
  // n * 2^n + 1, the power of two taken as a left shift - 3, 9, 25, 65, 161, 385, 897, ...
  //
  // The shift is machine-word bound, so exact terms run out at n = 57, which is where
  // generated.php stops; from n = 58 the product leaves the integer range as a float, which
  // ends the build. Past n = 62 the shift itself has no answer - 1 << 64 is 0, and n = 64 on
  // gave the term 1 for every position - so those answer such a float at once, and below
  // position 0 there is no term: a negative shift ended the request on "Bit shift by
  // negative number".

  if ( ! pqBoolWhole ( $pqLoop ) or $pqLoop < 0 )
    return FALSE;

  if ( $pqLoop > 62 )
    return (float) PHP_INT_MAX * 2;

  return (1 << $pqLoop) * $pqLoop + 1;

?>
