<?php

  // The body of a chart, whichever way it is written: {chart 'bar', data='sales', label='month',
  // value='amount'/}, the chart: type - {chart:bar data='sales'/}, or a bare {gauge value=72/}
  // when nothing else has the name - and {sparkline data='visits'/}. The caller sets
  // $padChartKind; a kind is a file of charts/types/, and the files there are all the kinds
  // there are (padChartTypes, lib/chart.php). The data is a {data} store, a sequence store,
  // the page's array or a _data file named by data=, or the first rows terms of a sequence=
  // type; title= names the chart for a screen reader.
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
  // default occurrence back (padChartGather). A chart without points answers nothing, and
  // its @else@ shows.

  include_once PAD . 'charts/_lib/chart.php';

  if ( ! padChartType ( $padChartKind ) ) {
    if ( $padCheckSyntax )
      padError ( "the chart has no kind named '" . padMakeSafe ( $padChartKind, 20 ) . "' - " . implode ( ', ', padChartTypes () ) );
    $padChartKind = 'bar';
  }

  // The kind drawn goes on the xref record, which the reference application shows: the pages
  // that draw a chart of each kind.

  if ( $padInfoXref ?? FALSE )
    padInfoXref ( 'chart', $padChartKind );

  $padChartSource = $padContent;
  $padContent     = '';
  $padChartRows   = NULL;

  // The handling options - sort=, first=, where= ... - work on the rows drawn, as on any
  // data tag's, where they ran after the tag on its one default occurrence: a "top 5"
  // chart drew every row in stored order, and a where= failed on a field no row had.

  if ( padChartHandles () ) {
    $padData [$pad] = padData ( padChartRows ( $padChartSource, padChartWhat () ) );
    include PAD . 'handling/handling.php';
    $padChartRows       = $padData [$pad];
    $padHandDone [$pad] = TRUE;
  }

  // A chart without points answers FALSE, a miss, so its @else@ shows: the '' it drew
  // was a value like any other - a hit - and "No sales yet" never appeared.

  // client: the browser draws it - the server hands it the rows and the options
  // (charts/_lib/client.php).

  $padChartClient = padTagParm ( 'client', FALSE );
  $padChartClient = ( $padChartClient !== FALSE and $padChartClient !== '' and $padChartClient !== 0 and $padChartClient !== '0' );

  $padChartSvg = $padChartClient ? padChartClient ( $padChartKind, $padChartSource, $padChartRows )
                                 : padChartTag    ( $padChartKind, $padChartSource, $padChartRows );

  return ( $padChartSvg === '' ) ? FALSE : $padChartSvg;

?>
