<?php

  // A chart drawn in the browser: {chart 'bar', data='sales', client/} - the client option of
  // every chart kind. The server gathers what the kind draws from - its rows, as padChartGather
  // gives them, after the handling options, and the tag's options - and writes them into the
  // page as JSON; the SVG is drawn in the browser by www/assets/charts/, the PHP of this
  // directory written again in JavaScript, a file for a file and a function for a function,
  // so the chart comes out the same as the server draws it.
  //
  // The page gets core.js once and the file of each family it draws once - inline, as the
  // widgets and the games get their scripts (padWidgetScript), with this request's nonce when
  // $padCsp asks for one - then, per chart, a span holding the JSON and a script that puts the
  // SVG in its place (PadChart.mount). PadChart.mountAll ( element ) draws the charts of a
  // part of a page that came in later, where scripts written by innerHTML do not run.
  //
  // The options are the kind's to read in the browser, so the server marks them all read: the
  // strict check reports no option of a chart drawn there - one misspelt too. What the kind
  // reports as a mistake - eight series at most - the browser shows in the chart's place. A
  // chart without rows answers nothing, and its @else@ shows, as on the server; one whose
  // rows the browser finds no point in is left out there.

  // The files of www/assets/charts/ a kind is drawn with, beside core.js.

  const padChartClientFiles = [
    'arc'          => [ 'arc' ],                        'area'         => [ 'area' ],
    'bar'          => [ 'pie', 'series' ],              'boxplot'      => [ 'distribution' ],
    'bubble'       => [ 'scatter' ],                    'bullet'       => [ 'bullet' ],
    'calendar'     => [ 'calendar' ],                   'candlestick'  => [ 'candlestick' ],
    'chord'        => [ 'chord' ],                      'column'       => [ 'pie', 'series' ],
    'density'      => [ 'distribution', 'density' ],    'donut'        => [ 'pie', 'series' ],
    'dual'         => [ 'dual' ],                       'dumbbell'     => [ 'dumbbell' ],
    'errorbar'     => [ 'errorbar' ],                   'funnel'       => [ 'funnel' ],
    'gantt'        => [ 'calendar', 'gantt' ],          'gauge'        => [ 'gauge' ],
    'hbar'         => [ 'pie', 'series' ],              'heatmap'      => [ 'heatmap' ],
    'histogram'    => [ 'distribution' ],               'line'         => [ 'pie', 'series' ],
    'marimekko'    => [ 'marimekko' ],                  'multiples'    => [ 'multiples' ],
    'network'      => [ 'network' ],                    'orgchart'     => [ 'orgchart' ],
    'pack'         => [ 'hierarchy', 'pack' ],          'parallel'     => [ 'parallel' ],
    'parliament'   => [ 'parliament' ],                 'pie'          => [ 'pie', 'series' ],
    'proportional' => [ 'proportional' ],               'pyramid'      => [ 'funnel' ],
    'radar'        => [ 'radar' ],                      'radialbar'    => [ 'radial' ],
    'rings'        => [ 'rings' ],                      'rose'         => [ 'radial' ],
    'sankey'       => [ 'sankey' ],                     'scatter'      => [ 'scatter' ],
    'sparkline'    => [ 'pie', 'series' ],              'spiral'       => [ 'spiral' ],
    'stream'       => [ 'area' ],                       'sunburst'     => [ 'hierarchy' ],
    'treemap'      => [ 'hierarchy' ],                  'venn'         => [ 'venn' ],
    'violin'       => [ 'distribution', 'density' ],    'waffle'       => [ 'waffle' ],
    'waterfall'    => [ 'waterfall' ],
    'ecdf'         => [ 'distribution', 'ecdf' ] ];

  function padChartClient ( $kind, $source = '', $handled = NULL, $palette = '' ) {

    global $pad, $padPrm, $padChartNow, $padCheckSyntax;

    // The rows as the kind's own file reads them - a gauge of one number needs none.

    $before      = $padChartNow ?? NULL;
    $padChartNow = [ 'kind' => $kind, 'source' => $source, 'handled' => $handled, 'name' => '' ];

    $rows = padChartGather ( $kind == 'gauge' );
    $name = padChartSeqName ();

    $padChartNow = $before;

    $params = $padPrm [$pad] ?? [];

    foreach ( array_keys ( $params ) as $param )
      padDone ( $param );

    if ( ! $rows and ! ( $kind == 'gauge' and isset ( $params ['value'] ) ) )
      return '';

    unset ( $params ['client'], $params ['data'] );

    $spec = [
      'kind'   => $kind,
      'id'     => padChartId ( "client-$kind", [ $rows, $params, $name ], '', 0, 0 ),
      'rows'   => padChartClientValue ( $rows ),
      'params' => padChartClientValue ( $params, TRUE ),
      'name'   => $name,
      'what'   => padChartWhat (),
      'nonce'  => padStyleNonce (),
      'check'  => (bool) $padCheckSyntax,
      'zone'   => padDateZone ()->getName ()
    ];

    // A chart given palette= gets the class and the rules of each number of slots it may use
    // (_lib/palette.php) and www/assets/charts/palette.js, which puts the ones it uses on the svg.

    if ( $palette !== '' )
      $spec ['palette'] = padChartPaletteStyles ( $palette );

    $json = json_encode ( $spec, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE );

    $scripts = padWidgetScript ( 'chart', padChartClientFile ( 'core' ) );

    if ( $palette !== '' )
      $scripts .= padWidgetScript ( 'chart-palette', padChartClientFile ( 'palette' ) );

    foreach ( padChartClientFiles [$kind] ?? [] as $file )
      $scripts .= padWidgetScript ( "chart-$file", padChartClientFile ( $file ) );

    return $scripts
         . '<span class="pad-chart-client" data-pad-chart="' . htmlspecialchars ( $json, ENT_QUOTES, 'UTF-8' ) . '"></span>'
         . padWidgetScript ( 'chart-mount-' . $spec ['id'], 'PadChart.mount(document.currentScript.previousElementSibling)' );

  }

  // A value for the browser: a PHP array as { m: [ [ key, value ] ... ] }, so its keys keep
  // their order and their kind - a JavaScript object puts the key 2024 before the key 'b' -
  // a date as the text padChartText writes of it, another object as its array, and a number
  // JSON cannot hold as nothing. $object - the options - is a plain object of its own names.

  function padChartClientValue ( $value, $object = FALSE ) {

    if ( $value instanceof DateTimeInterface )
      return $value->format ( 'Y-m-d H:i:sP' );

    if ( $value instanceof Stringable )
      return (string) $value;

    if ( is_object ( $value ) or is_resource ( $value ) )
      $value = padToArray ( $value );

    if ( is_float ( $value ) and ! is_finite ( $value ) )
      return NULL;

    if ( ! is_array ( $value ) )
      return $value;

    if ( $object ) {
      $out = [];
      foreach ( $value as $key => $one )
        $out [ (string) $key ] = padChartClientValue ( $one );
      return (object) $out;
    }

    $pairs = [];

    foreach ( $value as $key => $one )
      $pairs [] = [ $key, padChartClientValue ( $one ) ];

    return [ 'm' => $pairs ];

  }

  // A file of www/assets/charts/ (padWidgetFile): linked, or written in as it stands - its
  // comments and all, without the line break that ends it.

  function padChartClientFile ( $name ) {

    return padWidgetFile ( "charts/$name.js" );

  }

?>
