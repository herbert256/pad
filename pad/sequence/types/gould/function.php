<?php

  // Build strategy 'function' for the gould sequence: pqGould($n) returns the number of odd
  // entries in row n-1 of Pascal's triangle, equivalently 2 raised to the number of one
  // bits in n-1. Counting from $pqLoop = 1 the terms are 1, 2, 2, 4, 2, 4, 4, 8, 2, ...
  //
  // The row is walked with the multiplicative binomial recurrence rather than built, the
  // odd coefficients being counted on the way. Once the running coefficient passes
  // PHP_INT_MAX the count so far is returned, so a large argument yields a partial answer
  // instead of an error. This type has no generated.php cache.

  function pqGould ($n) {

    $x = $c = 1;

    for ($i = 1; $i <= $n; $i++) {

      $c = $c * ($n - $i) / $i;

      if ( $c > PHP_INT_MAX)
        return $x;

      $c = (int) $c;

      if ($c % 2 == 1)
        $x++;

    }

    return $x;

  }

?>