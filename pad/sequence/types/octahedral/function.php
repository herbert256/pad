<?php

  // Function build for octahedral: pqOctahedral($n) = n(2n^2 + 1)/3, the number of spheres
  // in an octahedral pile of n layers - 1, 6, 19, 44, 85, 146, 231, ...

  function pqOctahedral ($n) {

    return $n * (2 * $n * $n + 1) / 3;

  }

?>