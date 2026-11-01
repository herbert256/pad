<?php

  // sunburst - The same hierarchy as rings around the total. Options: label, value, levels;
  // width= and height=, 480 x 280 unless given.

  return padChartHierarchy ( 'sunburst', padChartGather (), ...padChartSize ( 480, 280 ) );

?>
