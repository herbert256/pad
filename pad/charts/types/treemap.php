<?php

  // treemap - Nested rectangles (squarified), levels the fields above the parts, outermost
  // first. Options: label, value, levels; width= and height=, 600 x 400 unless given.

  return padChartHierarchy ( 'treemap', padChartGather (), ...padChartSize ( 600, 400 ) );

?>
