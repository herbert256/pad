<?php

  // stream - A streamgraph: the value fields stacked in smooth curves around a wandering
  // middle (the wiggle offset, early peaks inside), no value axis, the labels below and a
  // legend above. Options: label, value; width= and height=, 600 x 300 unless given.

  return padChartArea ( 'stream', padChartGather (), ...padChartSize ( 600, 300 ) );

?>
