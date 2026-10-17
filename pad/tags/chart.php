<?php

  // {chart 'bar', data='sales', label='month', value='amount'} - an inline SVG chart, drawn
  // on the server by tags/_lib/chart.php and tags/_lib/chart/: bar (or column), hbar, line,
  // sparkline, pie, donut, scatter, bubble, heatmap, sankey, network, treemap, sunburst,
  // gauge, radar, waterfall, histogram, boxplot, calendar, gantt, area, stream, multiples,
  // candlestick, dual, spiral, rose, radialbar, funnel, pyramid, waffle, marimekko,
  // parliament, venn, density, violin, bullet, rings, parallel, proportional, chord, arc,
  // pack or orgchart.
  // The data is
  // a {data} store, a sequence store, the page's array or a _data file named by data=, or
  // the first rows terms of a sequence= type; title= names the chart for a screen reader.
  //
  // As a pair the content is the data - JSON, YAML, XML or CSV, recognised as {data}
  // recognises it - when neither data= nor sequence= names it:
  //
  //   {chart 'bar', label='month', value='amount'}
  //     month,amount
  //     Jan,120
  //   {/chart}
  //
  // The content is taken as it stands, before the level walks it, so the braces of JSON
  // need no {ignore}; the level is left no content of its own to render.
  //
  // The data= option has already been read into this level's data by level/start.php, and
  // the level would iterate it, writing the chart once per row - so the level gets its one
  // default occurrence back. A chart without points answers nothing, and its @else@ shows.

  $padChartKind = strtolower ( trim ( (string) $padParm ) );

  $padChartKinds = [ 'bar', 'column', 'hbar', 'line', 'sparkline', 'pie', 'donut', 'scatter', 'bubble',
                     'heatmap', 'sankey', 'network', 'treemap', 'sunburst', 'gauge', 'radar', 'waterfall',
                     'histogram', 'boxplot', 'calendar', 'gantt', 'area', 'stream', 'multiples', 'candlestick',
                     'dual', 'spiral', 'rose', 'radialbar', 'funnel', 'pyramid', 'waffle', 'marimekko',
                     'parliament', 'venn', 'violin', 'density', 'bullet', 'rings', 'parallel', 'proportional',
                     'chord', 'arc', 'pack', 'orgchart' ];

  if ( ! in_array ( $padChartKind, $padChartKinds ) ) {
    if ( $padCheckSyntax )
      padError ( "the chart has no kind named '" . padMakeSafe ( $padChartKind, 20 ) . "' - " . implode ( ', ', $padChartKinds ) );
    $padChartKind = 'bar';
  }

  $padChartSource = $padContent;
  $padContent     = '';
  $padChartRows   = NULL;

  // The handling options - sort=, first=, where= ... - work on the rows drawn, as on any
  // data tag's, where they ran after the tag on its one default occurrence: a "top 5"
  // chart drew every row in stored order, and a where= failed on a field no row had.

  if ( padChartHandles () ) {
    $padData [$pad] = padData ( padChartRows ( $padChartSource ) );
    include PAD . 'handling/handling.php';
    $padChartRows       = $padData [$pad];
    $padHandDone [$pad] = TRUE;
  }

  // A chart without points answers FALSE, a miss, so its @else@ shows: the '' it drew
  // was a value like any other - a hit - and "No sales yet" never appeared.

  $padChartSvg = padChartTag ( $padChartKind, $padChartSource, $padChartRows );

  return ( $padChartSvg === '' ) ? FALSE : $padChartSvg;

?>
