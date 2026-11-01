<?php

  // sankey - Flows from source to target as wide as value, in columns along the longest path;
  // flows that go round in a circle are an error. Options: source, target, value; width= and
  // height=, 600 x 400 unless given.

  return padChartSankey ( padChartGather (), ...padChartSize ( 600, 400 ) );

?>
