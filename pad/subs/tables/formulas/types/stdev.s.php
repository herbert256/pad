<?php

  // stdev.s - STDEV.S(number, ...) is STDEV under the name Excel gave it in 2010: the standard
  // deviation of a sample - divided by the count less one; of fewer than two numbers #DIV/0!.
  //
  //   =STDEV.S(2, 4, 4, 4, 5, 5, 7, 9)   2.138089935
  //   =STDEV.S(B2:B7)                    7.118052168

  return include __DIR__ . '/stdev.php';

?>
