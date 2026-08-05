<?php

  // Build strategy 'function' for the decagonal sequence: pqDecagonal($n) returns the nth
  // ten-sided figurate number, 4n^2 - 3n - 1, 10, 27, 52, 85, 126, ...

  function pqDecagonal ($n) {

    return 4 * $n * $n - 3 * $n;

  }

?>