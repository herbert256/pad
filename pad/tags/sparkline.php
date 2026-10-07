<?php

  // {sparkline data='visits', value='count'} - a word-sized line chart, no axes, to stand in
  // a sentence or a table cell. {chart 'sparkline', ...} by another name; see tags/chart.php.

  $padChartSource = $padContent;
  $padContent     = '';

  return padChartTag ( 'sparkline', $padChartSource );

?>
