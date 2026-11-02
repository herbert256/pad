<?php

  // A chart uses {chart:bar data='sales', label='month', value='amount'/},
  // {chart:gauge value=72/} or another kind in subs/charts/types/. The caller sets
  // $padChartKind. Data comes from a store, page array or _data file named by data=,
  // or the first rows terms of sequence=. title= names the chart for a screen reader.
  //
  // As a pair the content is the data - JSON, YAML, XML or CSV, recognised as {format:auto}
  // recognises it - when neither data= nor sequence= names it:
  //
  //   {chart:bar label='month', value='amount'}
  //     month,amount
  //     Jan,120
  //   {/chart:bar}
  //
  // The content is taken as it stands, before the level walks it, so the braces of JSON
  // need no {pad:ignore}; the level is left no content of its own to render.
  //
  // The data= option has already been read into this level's data by level/start.php, and
  // the level would iterate it, writing the chart once per row - so the level gets its one
  // default occurrence back (padChartGather). A chart without points answers nothing, and
  // its @else@ shows.

  include_once PAD . 'subs/charts/_lib/chart.php';

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
  //
  // A where= that leaves no row has the handling stand in with the one empty row of a level
  // that shows its @else@ (or its else=), which is no row of the chart's: it draws none, where
  // that row failed a dumbbell or a candlestick with a field no row has.

  if ( padChartHandles () ) {
    $padData [$pad] = padData ( padChartRows ( $padChartSource, padChartWhat () ) );
    include PAD . 'handling/handling.php';
    $padChartRows       = ( $padElse [$pad] or ( $padHandStoodIn [$pad] ?? 0 ) === $padLevelId [$pad] ) ? [] : $padData [$pad];
    $padHandDone [$pad] = TRUE;
  }

  // A chart without points answers FALSE, a miss, so its @else@ shows: the '' it drew
  // was a value like any other - a hit - and "No sales yet" never appeared.

  // client: the browser draws it - the server hands it the rows and the options
  // (subs/charts/_lib/client.php).

  $padChartClient = padTagParm ( 'client', FALSE );
  $padChartClient = ( $padChartClient !== FALSE and $padChartClient !== '' and $padChartClient !== 0 and $padChartClient !== '0' );

  // palette= draws the chart in the colours of a palette of subs/palettes/types/
  // (_lib/palette.php) - on the server here, in the browser from what client.php hands over.

  $padChartPalette = padChartPaletteName ();

  $padChartSvg = $padChartClient ? padChartClient ( $padChartKind, $padChartSource, $padChartRows, $padChartPalette )
                                 : padChartPaletteApply ( padChartTag ( $padChartKind, $padChartSource, $padChartRows ), $padChartPalette );

  return ( $padChartSvg === '' ) ? FALSE : $padChartSvg;

?>
