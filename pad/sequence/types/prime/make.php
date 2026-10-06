<?php

  // Make build for prime: maps the loop value onto the nth prime through pqPrime(), so
  // from/to/increment index the primes instead of the integers. Reached with build=make,
  // since pqBuild() prefers loop.php, and identical in effect to build=function.

  // A position is a whole number: a word or a fraction among the values a play is handed has
  // no term here, where it ended the request on the arithmetic or the array key it made.

  if ( ! pqBoolWhole ( $pqLoop ) )
    return FALSE;

  return pqPrime ( (int) $pqLoop );

?>
