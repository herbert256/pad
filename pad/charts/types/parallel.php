<?php

  // parallel - Parallel coordinates: an upright axis per measure of value (two or more, else
  // every numeric field), each scaled to its own round range, named on top; a line per row,
  // coloured by color with a legend or else one lighter colour. Options: label, value, color;
  // width= and height=, 600 x 400 unless given.

  return padChartParallel ( padChartGather (), ...padChartSize ( 600, 400 ) );

?>
