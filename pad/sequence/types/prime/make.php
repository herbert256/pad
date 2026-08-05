<?php

  // Make build for prime: maps the loop value onto the nth prime through pqPrime(), so
  // from/to/increment index the primes instead of the integers. Reached with build=make,
  // since pqBuild() prefers loop.php, and identical in effect to build=function.

  return pqPrime ( $pqLoop );

?>