<?php

  // pie - Parts of a whole - positive values only, past eight the smallest folded into a grey
  // 'Other', a legend with value and share; a donut shows the total in its middle. Options:
  // label, value; width= and height=, 480 x 280 unless given.

  return padChartPlain ( 'pie', padChartGather (), ...padChartSize ( 480, 280 ) );

?>
