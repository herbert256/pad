<?php

  // 200 values a step of 0.05 pixels high each on a plot 10 pixels high: every second step
  // is drawn at the height of the one before it, a tenth of a pixel being what a path writes.

  $many = [];

  for ( $i = 1; $i <= 200; $i++ )
    $many [] = [ 'ms' => $i ];

?>
