<?php

  // {chart 'bar', data='sales', label='month', value='amount'} - an inline SVG chart, drawn
  // on the server by padChart in lib/chart.php: 'bar', 'line' or 'sparkline'. The data is
  // a {data} store, a sequence store, the page's array or a _data file named by data=, or
  // the first rows terms of a sequence= type; title= names the chart for a screen reader.
  //
  // The data= option has already been read into this level's data by level/start.php, and
  // the level would iterate it, writing the chart once per row - so the level gets its one
  // default occurrence back. A chart without points answers nothing, and its @else@ shows.

  $padChartKind = strtolower ( trim ( (string) $padParm ) );

  if ( ! in_array ( $padChartKind, [ 'bar', 'line', 'sparkline' ] ) ) {
    if ( $padCheckSyntax )
      padError ( "the chart has no kind named '" . padMakeSafe ( $padChartKind, 20 ) . "' - bar, line or sparkline" );
    $padChartKind = 'bar';
  }

  return padChartTag ( $padChartKind );

?>
