<?php

  // area - Filled areas over the label axis, a line on each and a legend for several value
  // fields; stacked lays them on top of each other (the top edge the running total), percent
  // stacks every column to 100% - no negative values when stacked; a column per label carries
  // all values in its tooltip. Options: label, value, stacked, percent; width= and height=,
  // 600 x 300 unless given.

  return padChartArea ( 'area', padChartGather (), ...padChartSize ( 600, 300 ) );

?>
