<?php

  // waffle - A grid of cells squares (100, a 10 x 10 grid; 4 to 2500), each part its share in
  // its colour by the largest remainder so they add up exactly, filled row by row from the top
  // left; a legend with value and share; past eight parts the smallest folded into a grey
  // 'Other'. Options: label, value, cells; width= and height=, 480 x 280 unless given.

  return padChartWaffle ( padChartGather (), ...padChartSize ( 480, 280 ) );

?>
