<?php

  // The chart subsystem: inline SVG charts, written by the server - no JavaScript, so a chart
  // works in an e-mail and in print, and caches like any other output. With the option client
  // the browser draws the same SVG instead, from the rows and options the server hands it
  // (client.php, and www/assets/charts/ the drawing written again in JavaScript).
  //
  //   {chart:bar data='sales', label='month', value='amount'/}
  //   {chart:line data='visits', value='count', title='Visits per day'/}
  //   {chart:gauge value=72, unit='%'/}
  //   {chart:sparkline sequence='fibonacci', rows=12/}
  //
  // A chart kind is a file of subs/charts/types/: the kinds there are are the kinds a chart has
  // (padChartTypes, lib/chart.php). The name after the colon selects it: {chart:pie} or
  // {chart:sparkline}. subs/charts/chart.php is the shared body; a kind's file
  // reads the rows with padChartGather, its size with padChartSize - its own default size -
  // and answers the SVG, drawn by the functions of this directory.
  //
  //   padChartRows       the rows behind a chart: a {format:auto} store or a sequence store by name,
  //                      the page's own array of that name, a _data file, or the data=
  //                      literal the data option already read - or the first rows terms of a
  //                      sequence, or the content of a pair, {chart:bar}...{/chart:bar}, read
  //                      as {format:auto} reads its own: JSON, YAML, XML or CSV, told apart on sight
  //                      (type= names it)
  //   padChartPoints     the rows as [ label, value ] points: value= names the field (else
  //                      the first numeric one), label= the field for the axis (else the
  //                      first other one, else the row number); a row without a finite
  //                      number is left out
  //   padChartPlain      bar, hbar, line, sparkline, pie and donut, of one series or several
  //   padChart           the SVG of bar, line or sparkline of one series
  //
  // The other files of this directory draw the other kinds - scatter.php scatter and bubble,
  // hierarchy.php treemap and sunburst, and so on - and the parts they share: the opening,
  // the style and the number format. They are all included with this file, which is included
  // when a page draws a chart; {map:auto}, {timeline:horizontal}, {table:standard}, {calendar:month} and {diagram:auto}, that
  // draw with its parts, include it themselves. padChartAttr and padChartDedent, which tags
  // that draw no chart use as well, are in lib/chart.php, loaded on every request.
  //
  // Accessible: the svg has role="img" and is labelled by its <title> and a <desc> that
  // lists the values, so a screen reader hears the data; each bar and point carries a
  // <title> of its own, the browser's tooltip on hover. The colours are CSS custom
  // properties with light and dark defaults - --pad-chart-series, --pad-chart-text,
  // --pad-chart-grid and --pad-chart-surface - which a page overrides on .pad-chart.
  //
  // The parts are found in the listing of subs/charts/_lib/ kept between requests (padDirList,
  // lib/dirs.php), the names glob's *.php matched - not hidden, ending in .php - in the same
  // order: the glob read the directory in every request that drew a chart, some 60 us.

  foreach ( padDirList ( PAD . 'subs/charts/_lib' ) ?: [] as $padChartPart )
    if ( $padChartPart [0] != '.' and str_ends_with ( $padChartPart, '.php' ) and $padChartPart != 'chart.php' )
      include_once PAD . "subs/charts/_lib/$padChartPart";

  function padChartRows ( $source = '', $what = 'chart' ) {

    global $pad, $padPrm, $padData, $padDataStore, $pqStore, $padCheckSyntax;

    $sequence = $padPrm [$pad] ['sequence'] ?? '';

    if ( $sequence !== '' and $sequence !== TRUE ) {

      $rows = max ( 1, min ( 1000, (int) padTagParm ( 'rows', 10 ) ) );

      if ( ! preg_match ( '/^[a-zA-Z][a-zA-Z0-9]*$/', (string) $sequence ) or ! pqSeq ( $sequence ) ) {
        if ( $padCheckSyntax )
          padError ( "there is no sequence named '" . padMakeSafe ( $sequence, 30 ) . "' for the $what" );
        return [];
      }

      return pqArray ( $sequence, '', "rows=$rows" );

    }

    if ( ! isset ( $padPrm [$pad] ['data'] ) and trim ( (string) $source ) !== '' )
      return padData ( padChartDedent ( $source ), padTagParm ( 'type' ) );

    if ( ! isset ( $padPrm [$pad] ['data'] ) ) {
      if ( $padCheckSyntax )
        padError ( "the $what has no data - data='name', sequence='name' or the data between {" . padTagDisplay () . "} and {/" . padTagDisplay () . "}" );
      return [];
    }

    $name = $padPrm [$pad] ['data'];

    if ( is_string ( $name ) and padValidVar ( $name ) ) {

      if ( isset ( $padDataStore [$name] ) ) return $padDataStore [$name];
      if ( isset ( $pqStore      [$name] ) ) return $pqStore      [$name];

      if ( isset ( $GLOBALS [$name] ) and is_array ( $GLOBALS [$name] ) )
        return $GLOBALS [$name];

      if ( padFieldCheck ( $name ) and is_array ( $field = padFieldValue ( $name ) ) )
        return $field;

      if ( $file = padDataFileName ( $name ) )
        return padDataFileData ( $file );

      if ( $padCheckSyntax and ! padStrHidden ( $name ) )
        padError ( "there is no data named '$name' for the $what" );

      return [];

    }

    return $padData [$pad];

  }

  // Whether the chart has handling options to apply to its rows - sort=, first=, where= ...
  // as any data tag has them; not beside a sequence=, whose rows= is its own.

  function padChartHandles () {

    global $pad, $padParms, $padPrm;

    if ( isset ( $padPrm [$pad] ['sequence'] ) )
      return FALSE;

    foreach ( $padParms [$pad] ?? [] as $one )
      if ( $one ['padPrmKind'] == 'option' and is_string ( $one ['padPrmName'] ) and file_exists ( PAD . "handling/types/{$one ['padPrmName']}.php" )
           and ! in_array ( $one ['padPrmName'], [ 'negative', 'orderly', 'duplicates', 'atLeastOnce', 'left', 'right', 'both', 'sum', 'avg', 'min', 'max' ] ) )
        return TRUE;

    return FALSE;

  }

  // A chart of one kind: its file in subs/charts/types/ draws it. The file runs in this function,
  // with the kind, the source and the rows the handling options left at hand to padChartGather
  // in $padChartNow - put back afterwards, for a chart drawn while another one is. A kind
  // reads only the options it draws with, so an option of another kind - stacked on a line
  // chart - is one that nothing reads, which the strict check reports.
  //
  // A chart without points draws nothing, and its @else@ shows: a kind returns before it
  // reads the options it would have drawn with - title=, names=, ranked - so then every
  // option is marked read, as for a chart drawn in the browser (client.php), where a
  // dumbbell, errorbar, candlestick, gantt, box plot, waterfall or bullet chart without
  // rows and with a title= failed with "an option that nothing reads".

  function padChartTag ( $kind, $source = '', $handled = NULL ) {

    global $padChartNow;

    $before      = $padChartNow ?? NULL;
    $padChartNow = [ 'kind' => $kind, 'source' => $source, 'handled' => $handled, 'name' => '' ];

    $svg = include PAD . "subs/charts/types/$kind.php";

    $padChartNow = $before;

    if ( $svg === '' )
      foreach ( array_keys ( $GLOBALS ['padPrm'] [$GLOBALS ['pad']] ?? [] ) as $param )
        padDone ( $param );

    return $svg;

  }

  // The rows the chart draws, and the level's data put back to its one default occurrence:
  // the data= option has already been read into this level's data by level/start.php, and
  // the level would iterate it, writing the chart once per row. With $optional - a gauge of
  // one number - a chart given value= and no data, sequence or content needs no rows: a
  // value that is no number too, a metric that could not be read, '' or 'n/a', draws nothing
  // and its @else@ shows, where the chart was "without data" it never needed.

  function padChartGather ( $optional = FALSE ) {

    global $pad, $padData, $padPrm, $padChartNow;

    $sequence = padTagParm ( 'sequence' );
    $source   = $padChartNow ['source'] ?? '';
    $handled  = $padChartNow ['handled'] ?? NULL;

    if ( $handled !== NULL )
      $rows = $handled;
    elseif ( $optional and ! isset ( $padPrm [$pad] ['data'] ) and ( $sequence === '' or $sequence === TRUE )
             and trim ( (string) $source ) === '' and isset ( $padPrm [$pad] ['value'] ) )
      $rows = [];
    else
      $rows = padChartRows ( $source, padChartWhat () );

    $padData [$pad] = padDefaultData ();

    if ( $sequence !== '' and $sequence !== TRUE )
      $padChartNow ['name'] = ucfirst ( (string) $sequence ) . ', the first ' . count ( $rows ) . ' terms';

    return $rows;

  }

  // The name a chart drawn from a sequence= has - "Fibonacci, the first 12 terms" - or ''.

  function padChartSeqName () {

    return $GLOBALS ['padChartNow'] ['name'] ?? '';

  }

  // The size of the chart: width= and height=, else the default its kind's file gives.

  function padChartSize ( $width, $height ) {

    return [ max ( 20, (int) padTagParm ( 'width',  $width  ) ),
             max ( 10, (int) padTagParm ( 'height', $height ) ) ];

  }

  // The tag the chart is written as, for what a message says: {chart:sparkline}, {chart:bar}, and
  // {chart:bar} or {chart:bar} for the chart type.

  function padChartWhat () {

    global $pad, $padTag, $padType, $padPrefix;

    if ( ( $padType [$pad] ?? '' ) == 'chart' )
      return ( ( $padPrefix [$pad] ?? '' ) !== '' ? 'chart:' : '' ) . ( $padTag [$pad] ?? '' );

    return ( ( $padTag [$pad] ?? '' ) == 'sparkline' ) ? 'sparkline' : 'chart';

  }

  // Bar (and column), hbar, line, sparkline, pie and donut: one series as points, or several
  // - more than one field in value=, or an hbar - as a table of series.

  function padChartPlain ( $kind, $rows, $width, $height ) {

    $wide    = ( $kind == 'sparkline' );
    $seqName = padChartSeqName ();

    $label = (string) padTagParm ( 'label' );
    $value = (string) padTagParm ( 'value' );

    if ( $seqName !== '' )
      $name = $seqName;
    elseif ( $value !== '' )
      $name = implode ( ' and ', array_map ( 'ucfirst', padChartFields ( $value ) ) ) . ( $label !== '' ? " by $label" : '' );
    else
      $name = ucfirst ( $kind ) . ' chart';

    $title = (string) padTagParm ( 'title', $name );

    if ( $kind == 'pie' or $kind == 'donut' )
      return padChartPie ( $kind, padChartPoints ( $rows, $label, $value ), $title, $width, $height );

    $fields  = padChartFields ( $value );
    $stacked = ( $kind == 'bar' or $kind == 'hbar' ) ? padTagParm ( 'stacked', FALSE ) : FALSE;
    $stacked = ( $stacked !== FALSE and $stacked !== '' and $stacked !== 0 and $stacked !== '0' );

    if ( $kind == 'hbar' or ( ! $wide and count ( $fields ) > 1 ) ) {
      list ( $labels, $series ) = padChartTable ( $rows, $label, $fields );
      return padChartSeries ( $kind, $labels, $series, $title, $width, $height, $stacked );
    }

    return padChart ( $kind, padChartPoints ( $rows, $label, $value, ! $wide ), $title, $width, $height );

  }

  function padChartPoints ( $rows, $label, $value, $axis = TRUE ) {

    $points = [];
    $index  = 0;

    foreach ( (array) $rows as $key => $row ) {

      $index++;

      if ( is_object ( $row ) )
        $row = padToArray ( $row );

      if ( ! is_array ( $row ) ) {
        if ( padChartFinite ( $row, $axis ) )
          $points [] = [ is_string ( $key ) ? $key : (string) $index, $row + 0 ];
        continue;
      }

      $v = NULL;

      if ( $value !== '' )
        $v = $row [$value] ?? NULL;
      else
        foreach ( $row as $name => $field )
          if ( $name !== $label and is_numeric ( $field ) ) {
            $v = $field;
            break;
          }

      if ( ! padChartFinite ( $v, $axis ) )
        continue;

      $l = NULL;

      if ( $label !== '' )
        $l = $row [$label] ?? NULL;
      else
        foreach ( $row as $field )
          if ( is_scalar ( $field ) and ! is_numeric ( $field ) ) {
            $l = $field;
            break;
          }

      // No label field: the key of an object of label: value - { "Rent": 900 } is a row of
      // one field under the key Rent - else the row's number.

      if ( ! is_scalar ( $l ) )
        $l = is_string ( $key ) ? $key : $index;

      $points [] = [ (string) $l, $v + 0 ];

    }

    return $points;

  }

  // A value the chart can plot: a number that is finite - '1e999' is numeric, but it is
  // INF as a float, and the axis made no ticks of it and ended the page with an undefined
  // array key - and no larger than 1e300 either way, so the axis around it stays finite
  // too: 1.7e308 rounded its top tick up to INF and the tick loop never ended, and 1e308
  // beside -1e308 spanned INF and divided by zero. The bound is the axis's: a sparkline has
  // none and scales to its own minimum and maximum, so it takes every finite value.

  function padChartFinite ( $value, $axis = TRUE ) {

    return is_numeric ( $value ) and is_finite ( (float) $value ) and ( ! $axis or abs ( (float) $value ) <= 1e300 );

  }

  // A number as an axis or a tooltip writes it: thousands grouped, as many decimals as it
  // has up to three - the ticks of a step of 0.025 - and beyond that two, or two digits
  // past its leading zeros for a small one: error rates and probabilities all read 0.00,
  // ticks at 0.025 and 0.075 were labelled 0.03 and 0.08.

  function padChartNumber ( $n ) {

    // A share that overflows - a value near 1e300 over one near 1e-300 - is infinite: it is
    // written as such, where its log10 cast to an int ended the request.

    if ( ! is_finite ( (float) $n ) )
      return is_nan ( (float) $n ) ? '' : ( (float) $n > 0 ? '∞' : '-∞' );

    // A whole number - an int, or the text of one PHP's integers hold - is written with all
    // its digits: as a float past 2^53 it was rounded, the Mersenne prime 2^61-1 an even
    // ...952.

    if ( is_int ( $n ) or is_string ( $n ) and filter_var ( trim ( $n ), FILTER_VALIDATE_INT ) !== FALSE )
      return number_format ( (int) trim ( (string) $n ), 0, '.', ',' );

    $n = (float) $n;

    // The decimals a number has: a rest below the float's own precision is noise - 0.1 +
    // 0.2 - but no more than that, where a tolerance of a billionth of the number took .75
    // of two billion for noise and wrote 2,000,000,001.

    for ( $needed = 0; $needed < 15; $needed++ ) {
      $scaled = $n * 10 ** $needed;
      if ( abs ( $scaled - round ( $scaled ) ) <= abs ( $scaled ) * 16 * PHP_FLOAT_EPSILON )
        break;
    }

    // Past fifteen decimals - 5e-324 - a number is written as a power of ten, not as zeros.

    if ( $n != 0 and abs ( $n ) < 1e-14 )
      return sprintf ( '%.3g', $n );

    $decimals = ( $needed <= 3 or $n == 0 ) ? $needed : min ( $needed, max ( 2, 1 - (int) floor ( log10 ( abs ( $n ) ) ) ) );

    return number_format ( $n, $decimals, '.', ',' );

  }

  // Round tick values, about $count of them, that take in min and max. A tick is rounded
  // to ten digits past its step, not ten decimals: values of 1e-11 made every tick 0, and
  // the plot divided by the span between the first and the last. The step is no smaller
  // than the smallest normal float, which a span of a denormal value would underflow.

  function padChartTicks ( $min, $max, $count = 5 ) {

    // Values that differ only in their last bits - 0.3 beside 0.1 + 0.2 - are one value: a
    // step below the float spacing at the lowest tick, added to it, gave it back unchanged,
    // the loop of the ticks never ended and took the request down with 2 GB of memory. The
    // count of the ticks is bounded as well.

    if ( $min != $max and abs ( $max - $min ) <= max ( abs ( $min ), abs ( $max ) ) * 1e-12 )
      $max = $min;

    if ( $min == $max ) {
      if     ( $max > 0 ) $min = 0;
      elseif ( $max < 0 ) $max = 0;
      else                $max = 1;
    }

    $span = $max - $min;
    $step = max ( PHP_FLOAT_MIN, 10 ** floor ( log10 ( $span / $count ) ) );

    foreach ( [ 1, 2, 2.5, 5, 10 ] as $times )
      if ( $span / ( $step * $times ) <= $count ) {
        $step *= $times;
        break;
      }

    $low    = floor ( $min / $step ) * $step;
    $high   = ceil  ( $max / $step ) * $step;
    $digits = max ( 10, 10 - (int) floor ( log10 ( $step ) ) );
    $ticks  = [];

    for ( $tick = $low; $tick <= $high + $step / 2 and count ( $ticks ) < 1000; $tick += $step )
      $ticks [] = round ( $tick, $digits );

    return $ticks;

  }

  function padChartXY ( $n ) {

    return rtrim ( rtrim ( number_format ( $n, 1, '.', '' ), '0' ), '.' );

  }

  // The colours follow the page's own color-scheme through light-dark(): a page that says
  // color-scheme: light dark gets the dark steps in dark mode, and a light page keeps the
  // light ones whatever the system prefers - a chart that followed the system alone drew
  // pale text on a white page. A browser without light-dark() keeps the light steps. The
  // size stays the width and height given: a max-width of 100% - the responsive rule a
  // page can add on .pad-chart - shrank a chart in a table cell to nothing.
  //
  // The kinds beyond one series of bar, line or sparkline - $more - add the categorical
  // slots --pad-chart-1 to --pad-chart-8 - in this fixed order, the first being
  // --pad-chart-series - a grey
  // --pad-chart-other for what is folded together past the eighth, and the sequential
  // ramp --pad-chart-heat-0 to --pad-chart-heat-6 of the heatmap, stepped for each
  // surface: on a dark one the low end lies near the surface too.

  function padChartStyle ( $more = FALSE, $kind = '' ) {

    $roles = [ 'series'  => [ '#2a78d6', '#3987e5' ],
               'text'    => [ '#52514e', '#c3c2b7' ],
               'grid'    => [ '#e4e3df', '#3a3a37' ],
               'surface' => [ '#fcfcfb', '#1a1a19' ] ];

    if ( $more )
      $roles += [ '2' => [ '#eb6834', '#d95926' ], '3' => [ '#1baf7a', '#199e70' ],
                  '4' => [ '#eda100', '#c98500' ], '5' => [ '#e87ba4', '#d55181' ],
                  '6' => [ '#008300', '#008300' ], '7' => [ '#4a3aa7', '#9085e9' ],
                  '8' => [ '#e34948', '#e66767' ], 'other' => [ '#898781', '#898781' ],
                  'heat-0' => [ '#cde2fb', '#0d366b' ], 'heat-1' => [ '#9ec5f4', '#104281' ],
                  'heat-2' => [ '#6da7ec', '#184f95' ], 'heat-3' => [ '#3987e5', '#1c5cab' ],
                  'heat-4' => [ '#256abf', '#2a78d6' ], 'heat-5' => [ '#184f95', '#5598e7' ],
                  'heat-6' => [ '#0d366b', '#9ec5f4' ] ];

    $light = $both = '';

    foreach ( $roles as $role => list ( $day, $night ) ) {
      $light .= "--pad-chart-$role:$day;";
      $both  .= "--pad-chart-$role:light-dark($day,$night);";
    }

    return '<style' . padStyleNonce () . '>'
         . ":where(.pad-chart){{$light}}"
         . "@supports (color:light-dark(#000,#fff)){:where(.pad-chart){{$both}}}"
         . '.pad-chart text{fill:var(--pad-chart-text);font:11px system-ui,sans-serif}'
         . '.pad-chart .pc-grid{stroke:var(--pad-chart-grid);stroke-width:1}'
         . '.pad-chart .pc-axis{stroke:var(--pad-chart-text);stroke-width:1}'
         . '.pad-chart .pc-bar{fill:var(--pad-chart-series)}'
         . '.pad-chart .pc-bar:hover{opacity:.8}'
         . '.pad-chart .pc-line{fill:none;stroke:var(--pad-chart-series);stroke-width:2;stroke-linejoin:round;stroke-linecap:round}'
         . '.pad-chart .pc-area{fill:var(--pad-chart-series);opacity:.1}'
         . '.pad-chart .pc-dot{fill:var(--pad-chart-series);stroke:var(--pad-chart-surface);stroke-width:2}'
         . '.pad-chart .pc-hit{fill:transparent}'
         . ( $more ? padChartStyleMore () . padChartStyleParts ( $kind ) : '' )
         . '</style>';

  }

  // The rules of the kinds beyond one series of bar, line or sparkline: a fill and a 2px
  // line per slot, the 2px surface gap between fills that touch, the ring around marks that
  // overlap,
  // the heatmap steps, the sankey's bands, the network's links and the light label written
  // on a fill.

  function padChartStyleMore () {

    $css = ':where(.pad-chart){--pad-chart-1:var(--pad-chart-series)}';

    for ( $slot = 1; $slot <= 8; $slot++ )
      $css .= ".pad-chart .pc-c$slot{fill:var(--pad-chart-$slot)}"
            . ".pad-chart .pc-l$slot{fill:none;stroke:var(--pad-chart-$slot);stroke-width:2;stroke-linejoin:round;stroke-linecap:round}";

    for ( $step = 0; $step <= 6; $step++ )
      $css .= ".pad-chart .pc-h$step{fill:var(--pad-chart-heat-$step)}";

    return $css
         . '.pad-chart .pc-co{fill:var(--pad-chart-other)}'
         . '.pad-chart .pc-mark:hover{opacity:.8}'
         . '.pad-chart .pc-gap{stroke:var(--pad-chart-surface);stroke-width:2}'
         . '.pad-chart .pc-seg{stroke:var(--pad-chart-surface);stroke-width:1}'
         . '.pad-chart .pc-ring{stroke:var(--pad-chart-surface);stroke-width:2}'
         . '.pad-chart .pc-frame{fill:none;stroke:var(--pad-chart-surface);stroke-width:4}'
         . '.pad-chart .pc-bubble{fill-opacity:.75}'
         . '.pad-chart .pc-dp1{opacity:.75}.pad-chart .pc-dp2{opacity:.55}.pad-chart .pc-dp3{opacity:.4}'
         . '.pad-chart .pc-link{fill:var(--pad-chart-series);opacity:.3}'
         . '.pad-chart .pc-link:hover{opacity:.55}'
         . '.pad-chart .pc-edge{stroke:var(--pad-chart-text);stroke-opacity:.35;fill:none}'
         . '.pad-chart .pc-arrow{fill:var(--pad-chart-text);fill-opacity:.5}'
         . '.pad-chart .pc-trend{fill:none;stroke:var(--pad-chart-text);stroke-width:1.5;stroke-dasharray:4 3}'
         . '.pad-chart .pc-in{fill:#fff;stroke:rgba(0,0,0,.55);stroke-width:2px;paint-order:stroke;stroke-linejoin:round}'
         . '.pad-chart .pc-big{font-size:16px;font-weight:600}';

  }

  // The rules only the gauge, radar, box plot, calendar and gantt draw with - written into
  // those charts alone, so the others carry no rule they do not use.

  function padChartStyleParts ( $kind ) {

    $rules = [ 'gauge'     => '.pad-chart .pc-track{fill:var(--pad-chart-grid)}.pad-chart .pc-band{opacity:.3}'
                            . '.pad-chart .pc-target{stroke:var(--pad-chart-text);stroke-width:2.5}',
               'radar'     => '.pad-chart .pc-area-fill{fill-opacity:.15}.pad-chart .pc-tick{font-size:9px;opacity:.75}',
               'boxplot'   => '.pad-chart .pc-whisker{stroke:var(--pad-chart-text);stroke-width:1.5}.pad-chart .pc-box{fill-opacity:.35}'
                            . '.pad-chart .pc-median{stroke:var(--pad-chart-text);stroke-width:2.5}',
               'calendar'  => '.pad-chart .pc-none{fill:var(--pad-chart-grid)}',
               'gantt'     => '.pad-chart .pc-todo{opacity:.3}.pad-chart .pc-target{stroke:var(--pad-chart-text);stroke-width:2.5}' ];

    return $rules [$kind] ?? '';

  }

  // The opening of one chart's SVG: the svg element named by $title, a <desc> of the
  // entries in $desc - the first 60 - and the style; $hash is the data the id is made
  // from (padChartId), and the id comes back beside the text for the parts that refer to
  // it. The ids are made from the chart itself, so two charts on a page do not share a
  // title: a count per request gave a chart served from the fragment cache and a chart
  // rendered next to it the same pad-chart-1, and so did the charts of two nested passes -
  // two {common:example}s - each counting from one again. The same chart drawn twice in a request
  // is told apart by a number behind the second, kept in a static for the nested passes.

  function padChartOpen ( $kind, $hash, $desc, $title, $width, $height, $more = TRUE ) {

    $id    = padChartId ( $kind, $hash, $title, $width, $height );
    $count = count ( $desc );

    if ( $count > 60 )
      $desc = array_merge ( array_slice ( $desc, 0, 60 ), [ 'and ' . ( $count - 60 ) . ' more' ] );

    $svg = '<svg xmlns="http://www.w3.org/2000/svg" class="pad-chart pad-chart-' . $kind . '" role="img"'
         . " aria-labelledby=\"$id-title $id-desc\" width=\"$width\" height=\"$height\" viewBox=\"0 0 $width $height\">"
         . "<title id=\"$id-title\">" . padChartAttr ( $title ) . '</title>'
         . "<desc id=\"$id-desc\">" . padChartAttr ( implode ( '; ', $desc ) ) . '</desc>'
         . padChartStyle ( $more, $kind );

    return [ $id, $svg ];

  }

  // The id of a chart, made from what it draws - and, for a chart drawn in the browser
  // (client.php), from its rows and its options.
  //
  // Inside a {pad:cache} section that is rendering to be kept, the section's key is part of the
  // id too: kept, the chart came back on a later request beside the same chart drawn then,
  // and the two had one id - as padWidgetId has it for the widgets.

  function padChartId ( $kind, $hash, $title, $width, $height ) {

    global $pad, $padFragment;

    static $drawn = [];

    $seed = [ $kind, $hash, $title, $width, $height ];

    for ( $i = $pad ?? -1; $i >= 0; $i-- )
      if ( is_array ( $padFragment [$i] ?? NULL ) and ! $padFragment [$i] ['hit'] ) {
        $seed [] = $padFragment [$i] ['key'];
        break;
      }

    $id = 'pad-chart-' . substr ( md5 ( serialize ( $seed ) ), 0, 8 );

    $drawn [$id] = ( $drawn [$id] ?? 0 ) + 1;

    if ( $drawn [$id] > 1 )
      $id .= '-' . $drawn [$id];

    return $id;

  }

  // The SVG of a bar, line or sparkline chart.

  function padChart ( $kind, $points, $title, $width, $height ) {

    if ( ! $points )
      return '';

    $n    = count ( $points );
    $vals = array_column ( $points, 1 );
    $desc = [];

    foreach ( $points as $point )
      $desc [] = $point [0] . ': ' . padChartNumber ( $point [1] );

    list ( $id, $svg ) = padChartOpen ( $kind, $points, $desc, $title, $width, $height, FALSE );

    if ( $kind == 'sparkline' )
      return $svg . padChartSparkline ( $points, $width, $height ) . '</svg>';

    // The value axis: round ticks taking in zero, their labels on the left.

    $ticks = padChartTicks ( min ( 0, min ( $vals ) ), max ( 0, max ( $vals ) ), $height < 200 ? 3 : 5 );
    $low   = $ticks [0];
    $high  = end ( $ticks );

    $tickWidth = 0;
    foreach ( $ticks as $tick )
      $tickWidth = max ( $tickWidth, strlen ( padChartNumber ( $tick ) ) );

    $left   = max ( 24, (int) ceil ( $tickWidth * 6.5 ) + 10 );
    $right  = 10;
    $top    = 10;
    $bottom = 24;
    $plotW  = $width  - $left - $right;
    $plotH  = $height - $top  - $bottom;
    $band   = $plotW / $n;

    $y = fn ( $v ) => $top + ( $high - $v ) / ( $high - $low ) * $plotH;
    $x = fn ( $i ) => $left + ( $i + 0.5 ) * $band;

    foreach ( $ticks as $tick )
      $svg .= '<line class="pc-grid" x1="' . $left . '" x2="' . ( $width - $right ) . '" y1="' . padChartXY ( $y ( $tick ) ) . '" y2="' . padChartXY ( $y ( $tick ) ) . '"/>'
            . '<text x="' . ( $left - 6 ) . '" y="' . padChartXY ( $y ( $tick ) + 4 ) . '" text-anchor="end">' . padChartNumber ( $tick ) . '</text>';

    // The category labels under the plot, shortened to their band, and thinned out only
    // when a band holds less than about ten characters: long names were thinned instead -
    // of two bars only the first was named - and at six every date of a daily axis was cut
    // to "2026-0…", where every fourth stood whole.

    $labelWidth = 0;
    foreach ( $points as $point )
      $labelWidth = max ( $labelWidth, mb_strlen ( $point [0] ) * 6.5 + 8 );

    $every = max ( 1, (int) ceil ( min ( $labelWidth, 73 ) / max ( 1, $band ) ) );

    foreach ( $points as $i => $point )
      if ( $i % $every == 0 )
        $svg .= '<text x="' . padChartXY ( $x ( $i ) ) . '" y="' . ( $height - 8 ) . '" text-anchor="middle">' . padChartAttr ( padChartFit ( $point [0], $band * $every - 8 ) ) . '</text>';

    $base = $y ( 0 );

    if ( $kind == 'bar' ) {

      // Bars no thicker than 24, with a rounded data end of 4 and a square end at the base.

      $barW = max ( 1, min ( 24, $band * 0.7 ) );

      foreach ( $points as $i => $point ) {

        $x0  = $x ( $i ) - $barW / 2;
        $x1  = $x0 + $barW;
        $end = $y ( $point [1] );
        $r   = min ( 4, $barW / 2, abs ( $base - $end ) );
        $dir = ( $end <= $base ) ? 1 : -1;

        $d = 'M' . padChartXY ( $x0 ) . ',' . padChartXY ( $base )
           . 'V' . padChartXY ( $end + $dir * $r )
           . 'Q' . padChartXY ( $x0 ) . ',' . padChartXY ( $end ) . ' ' . padChartXY ( $x0 + $r ) . ',' . padChartXY ( $end )
           . 'H' . padChartXY ( $x1 - $r )
           . 'Q' . padChartXY ( $x1 ) . ',' . padChartXY ( $end ) . ' ' . padChartXY ( $x1 ) . ',' . padChartXY ( $end + $dir * $r )
           . 'V' . padChartXY ( $base ) . 'Z';

        $svg .= "<path class=\"pc-bar\" d=\"$d\"><title>" . padChartAttr ( $point [0] . ': ' . padChartNumber ( $point [1] ) ) . '</title></path>';

      }

    } else {

      // A 2px line over a 10% wash, the last point marked; every point has a larger
      // invisible target that carries its tooltip.

      $line = '';
      foreach ( $points as $i => $point )
        $line .= ( $i ? 'L' : 'M' ) . padChartXY ( $x ( $i ) ) . ',' . padChartXY ( $y ( $point [1] ) );

      $svg .= '<path class="pc-area" d="' . $line . 'L' . padChartXY ( $x ( $n - 1 ) ) . ',' . padChartXY ( $base ) . 'L' . padChartXY ( $x ( 0 ) ) . ',' . padChartXY ( $base ) . 'Z"/>'
            . '<path class="pc-line" d="' . $line . '"/>'
            . '<circle class="pc-dot" cx="' . padChartXY ( $x ( $n - 1 ) ) . '" cy="' . padChartXY ( $y ( $points [$n - 1] [1] ) ) . '" r="4"/>';

      $hit = padChartXY ( max ( 8, min ( 20, $band / 2 ) ) );

      foreach ( $points as $i => $point )
        $svg .= '<circle class="pc-hit" cx="' . padChartXY ( $x ( $i ) ) . '" cy="' . padChartXY ( $y ( $point [1] ) ) . "\" r=\"$hit\"><title>"
              . padChartAttr ( $point [0] . ': ' . padChartNumber ( $point [1] ) ) . '</title></circle>';

    }

    $svg .= '<line class="pc-axis" x1="' . $left . '" x2="' . ( $width - $right ) . '" y1="' . padChartXY ( $base ) . '" y2="' . padChartXY ( $base ) . '"/>';

    return "$svg</svg>";

  }

  // A word-sized line: no axes, the last point marked, scaled to its own minimum and
  // maximum - its job is the shape.

  function padChartSparkline ( $points, $width, $height ) {

    $vals = array_column ( $points, 1 );
    $min  = min ( $vals );
    $max  = max ( $vals );
    $n    = count ( $points );
    $pad  = 5;

    // Scaled by the span itself when it is a finite number other than 0; by the halves only
    // when it overflows - 1e308 beside -1e308 - since halving a span of one denormal, 5e-324
    // beside 0, made it 0 and the scale divided by it; and a span that is 0 - equal values,
    // which widening by one lost past 2^53 - draws a flat line through the middle.

    $x    = fn ( $i ) => $n == 1 ? $width / 2 : $pad + $i * ( $width - 2 * $pad ) / ( $n - 1 );
    $span = $max - $min;
    $half = $max / 2 - $min / 2;

    if ( is_finite ( $span ) and $span != 0 )
      $y = fn ( $v ) => $pad + ( $max - $v ) / $span * ( $height - 2 * $pad );
    elseif ( ! is_finite ( $span ) and $half != 0 )
      $y = fn ( $v ) => $pad + ( $max / 2 - $v / 2 ) / $half * ( $height - 2 * $pad );
    else
      $y = fn ( $v ) => $height / 2;

    $line = '';
    foreach ( $points as $i => $point )
      $line .= ( $i ? 'L' : 'M' ) . padChartXY ( $x ( $i ) ) . ',' . padChartXY ( $y ( $point [1] ) );

    return '<path class="pc-line" d="' . $line . '"/>'
         . '<circle class="pc-dot" cx="' . padChartXY ( $x ( $n - 1 ) ) . '" cy="' . padChartXY ( $y ( $points [$n - 1] [1] ) ) . '" r="3"/>';

  }

?>
