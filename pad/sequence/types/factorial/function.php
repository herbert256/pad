<?php

  // Function build for factorial: pqFactorial($n) is n! = 1 * 2 * ... * n - 1, 2, 6, 24, 120,
  // 720, ... from n = 1, 1 at position 0. 20! is the last inside the integer range: from 21!
  // the product is a float, handed back as it stands so the build ends on it, and a far
  // position does not multiply on to it. A position below 0, or between two, has no term.

  function pqFactorial ( $n ) {

    if ( ! pqBoolWhole ( $n ) or $n < 0 )
      return FALSE;

    for ( $product = 1, $i = 2; $i <= $n; $i++ ) {

      $product *= $i;

      if ( is_float ( $product ) )
        return $product;

    }

    return $product;

  }

?>
