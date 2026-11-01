<?php

  // pyramid - One value field: a triangle in layers, the first row at the apex, each layer's
  // area its value, named beside it with value and share. Two fields (value='men, women'): a
  // population pyramid - the first to the left, the second to the right of a middle column
  // naming the rows, the first row at the bottom, mirrored ticks below, a legend. Options:
  // label, value; width= and height=, 600 x 300 unless given.

  return padChartFunnel ( 'pyramid', padChartGather (), ...padChartSize ( 600, 300 ) );

?>
