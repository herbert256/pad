<?php

  // bar - Columns from a zero baseline; several value fields side by side, or one on the other
  // with stacked. Options: label, value, stacked; width= and height=, 600 x 300 unless given.

  return padChartPlain ( 'bar', padChartGather (), ...padChartSize ( 600, 300 ) );

?>
