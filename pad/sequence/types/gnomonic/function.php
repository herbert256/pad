<?php

  // Build strategy 'function' for the gnomonic sequence: pqGnomonic($n) returns 2n - 1, the
  // odd numbers 1, 3, 5, 7, 9, ... - the L-shaped gnomon that has to be added to one square
  // number to reach the next.

  function pqGnomonic ($n) {

    return 2 * $n -1;

  }

?>