<?php

  // {sparkline data='visits', value='count'} - a word-sized line chart, no axes, to stand in
  // a sentence or a table cell. {chart 'sparkline', ...} by another name; see tags/chart.php.

  $padChartSource = $padContent;
  $padContent     = '';
  $padChartRows   = NULL;

  // The handling options - sort=, first=, where= ... - work on the rows drawn, as on any
  // data tag's, where they ran after the tag on its one default occurrence: a "top 5"
  // chart drew every row in stored order, and a where= failed on a field no row had.

  if ( padChartHandles () ) {
    $padData [$pad] = padData ( padChartRows ( $padChartSource, 'sparkline' ) );
    include PAD . 'handling/handling.php';
    $padChartRows       = $padData [$pad];
    $padHandDone [$pad] = TRUE;
  }

  // A chart without points answers FALSE, a miss, so its @else@ shows: the '' it drew
  // was a value like any other - a hit - and "No sales yet" never appeared.

  $padChartSvg = padChartTag ( 'sparkline', $padChartSource, $padChartRows );

  return ( $padChartSvg === '' ) ? FALSE : $padChartSvg;

?>
