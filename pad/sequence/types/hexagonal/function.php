<?php

  // Build strategy 'function' for the hexagonal sequence: pqHexagonal($n) returns the nth
  // six-sided figurate number, n(2n - 1) - 1, 6, 15, 28, 45, 66, 91, ... These are also
  // the odd-indexed triangular numbers.

  function pqHexagonal ($n) {

    return $n * (2 * $n - 1);

  }

?>