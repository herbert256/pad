<?php

  // {sparkline data='visits', value='count'} - a word-sized line chart, no axes, to stand in
  // a sentence or a table cell. {chart 'sparkline', ...} by another name; see tags/chart.php.

  $padChartSource = $padContent;
  $padContent     = '';

  // A chart without points answers FALSE, a miss, so its @else@ shows: the '' it drew
  // was a value like any other - a hit - and "No sales yet" never appeared.

  $padChartSvg = padChartTag ( 'sparkline', $padChartSource );

  return ( $padChartSvg === '' ) ? FALSE : $padChartSvg;

?>
