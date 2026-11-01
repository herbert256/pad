<?php

  // heatmap - A grid of the categories x (columns) and y (rows), each cell one of seven steps
  // of one hue; rows with the same cell add up. Options: x, y, value; width= and height=, 600
  // x 300 unless given.

  return padChartHeatmap ( padChartGather (), ...padChartSize ( 600, 300 ) );

?>
