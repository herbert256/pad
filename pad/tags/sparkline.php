<?php

  // {sparkline data='visits', value='count'/} - a word-sized line chart, no axes, to stand in
  // a sentence or a table cell: {chart 'sparkline', ...} by another name, drawn by the chart
  // subsystem, charts/types/sparkline.php.

  $padChartKind = 'sparkline';

  return include PAD . 'charts/chart.php';

?>
