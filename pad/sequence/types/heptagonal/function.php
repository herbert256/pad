<?php

  // Build strategy 'function' for the heptagonal sequence: pqHeptagonal($n) returns the nth
  // seven-sided figurate number, n(5n - 3)/2 - 1, 7, 18, 34, 55, 81, 112, ...

  function pqHeptagonal ($n) {

    return ((5 * $n * $n) - (3 * $n)) / 2;

  }

?>