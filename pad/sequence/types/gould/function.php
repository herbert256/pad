<?php

  // Build strategy 'function' for the gould sequence: pqGould($n) returns the number of odd
  // entries in row n-1 of Pascal's triangle, equivalently 2 raised to the number of one
  // bits in n-1. Counting from $pqLoop = 1 the terms are 1, 2, 2, 4, 2, 4, 4, 8, 2, ...
  //
  // Kummer's theorem is what makes the second reading true, and it is the one used here:
  // the one bits of n-1 are counted and 2 is shifted that far. Walking the row through the
  // multiplicative binomial instead needs coefficients far past PHP_INT_MAX to stay exact -
  // the parity of the count broke from n = 63 - while counting bits stays whole throughout.
  //
  // An argument below 1 leaves the count at zero and the shift answers 1, so the loop is
  // never entered with a negative value. This type has no generated.php cache.

  function pqGould ($n) {

    $bits = 0;

    for ( $i = $n - 1; $i > 0; $i >>= 1 )
      $bits += $i & 1;

    return 1 << $bits;

  }

?>