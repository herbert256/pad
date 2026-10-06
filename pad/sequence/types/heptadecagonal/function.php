<?php

  // Build strategy 'function' for the heptadecagonal sequence: pqHeptadecagonal($n) returns
  // the nth seventeen-sided figurate number, n(15n - 13)/2 - 1, 17, 48, 94, 155, 231, ...

  function pqHeptadecagonal  ($n) {

    return pqProduct ( [ $n, 15 * $n - 13 ], 2 );

  }

?>
