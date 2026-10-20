<?php

  // Function build for primorial: pqPrimorial($n) is the product of the first n primes - 2, 6,
  // 30, 210, 2310, 30030, ... from n = 1, 1 at position 0. The fifteenth is the last inside
  // the integer range: the sixteenth prime, 53, takes the product past it, and that float
  // ends the build. A position below 0, or between two, has no term.

  function pqPrimorial ( $n ) {

    if ( ! pqBoolWhole ( $n ) or $n < 0 )
      return FALSE;

    $product = 1;

    foreach ( [ 2, 3, 5, 7, 11, 13, 17, 19, 23, 29, 31, 37, 41, 43, 47, 53 ] as $i => $prime ) {

      if ( $i >= $n )
        return $product;

      $product *= $prime;

    }

    return $product;

  }

?>
