<?php

  // A float below 0.0001 is written in E notation, 1.0E-5, so its decimals were never
  // counted and the sum was rounded to the one decimal of 0.5.

  $tiny = [ [ 'g' => 'a', 'x' => 0.00001 ], [ 'g' => 'a', 'x' => 0.5 ] ];

?>
