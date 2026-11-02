<?php

  // var.s - VAR.S(number, ...) is VAR under the name Excel gave it in 2010: the variance of a
  // sample - divided by the count less one; of fewer than two numbers #DIV/0!.
  //
  //   =VAR.S(2, 4, 4, 4, 5, 5, 7, 9)     4.571428571
  //   =VAR.S(B2:B7)                      50.66666667

  return include __DIR__ . '/var.php';

?>
