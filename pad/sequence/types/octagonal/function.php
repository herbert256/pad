<?php

  // Function build for octagonal: pqOctagonal($n) = 3n^2 - 2n, the nth octagonal number,
  // 1, 8, 21, 40, 65, 96, 133, ...

  function pqOctagonal ($n) {

    return $n * ( 3 * $n - 2 );

  }

?>
