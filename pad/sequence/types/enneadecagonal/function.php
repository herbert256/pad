<?php

  // Build strategy 'function' for the enneadecagonal sequence: pqEnneadecagonal($n) returns
  // the nth nineteen-sided figurate number, n(17n - 15)/2 - 1, 19, 54, 106, 175, 261, ...

  function pqEnneadecagonal ($n) {

    return ( 17 * $n * $n - 15 * $n ) / 2;

  }

?>