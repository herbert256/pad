<?php

  // Build strategy 'function' for the heptadecagonal sequence: pqHeptadecagonal($n) returns
  // the nth seventeen-sided figurate number, n(15n - 13)/2 - 1, 17, 48, 94, 155, 231, ...

  function pqHeptadecagonal  ($n) {

    return ( (15 * $n * $n) -  13 * $n) / 2;

  }

?>