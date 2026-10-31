<?php

  // bubble - A scatter whose dots have the area of size. Options: x, y, size, label, color,
  // trend; width= and height=, 600 x 300 unless given.

  return padChartScatter ( 'bubble', padChartGather (), padChartSeqName (), ...padChartSize ( 600, 300 ) );

?>
