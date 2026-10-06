<?php

  // Inline SVG charts - the {chart} and {sparkline} tags. The server writes the picture: no
  // JavaScript, so a chart works in an e-mail and in print, and caches like any other
  // output.
  //
  //   {chart 'bar', data='sales', label='month', value='amount'}
  //   {chart 'line', data='visits', value='count', title='Visits per day'}
  //   {sparkline data='visits', value='count'}
  //   {sparkline sequence='fibonacci', rows=12}
  //
  // padChartRows       the rows behind a chart: a {data} store or a sequence store by name,
  //                    the page's own array of that name, a _data file, or the data= literal
  //                    the data option already read - or the first rows terms of a sequence
  // padChartPoints     the rows as [ label, value ] points: value= names the field (else
  //                    the first numeric one), label= the field for the axis (else the first
  //                    other one, else the row number); a row without a finite number is
  //                    left out
  // padChart           the SVG: bar, line or sparkline
  //
  // Accessible: the svg has role="img" and is labelled by its <title> and a <desc> that
  // lists the values, so a screen reader hears the data; each bar and point carries a
  // <title> of its own, the browser's tooltip on hover. The colours are CSS custom
  // properties with light and dark defaults - --pad-chart-series, --pad-chart-text,
  // --pad-chart-grid and --pad-chart-surface - which a page overrides on .pad-chart.

  function padChartRows () {

    global $pad, $padPrm, $padData, $padDataStore, $pqStore, $padCheckSyntax;

    $sequence = $padPrm [$pad] ['sequence'] ?? '';

    if ( $sequence !== '' and $sequence !== TRUE ) {

      $rows = max ( 1, min ( 1000, (int) padTagParm ( 'rows', 10 ) ) );

      if ( ! preg_match ( '/^[a-zA-Z][a-zA-Z0-9]*$/', (string) $sequence ) or ! pqSeq ( $sequence ) ) {
        if ( $padCheckSyntax )
          padError ( "there is no sequence named '" . padMakeSafe ( $sequence, 30 ) . "' for the chart" );
        return [];
      }

      return pqArray ( $sequence, '', "rows=$rows" );

    }

    if ( ! isset ( $padPrm [$pad] ['data'] ) ) {
      if ( $padCheckSyntax )
        padError ( "the chart has no data - data='name' or sequence='name'" );
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

      if ( $padCheckSyntax )
        padError ( "there is no data named '$name' for the chart" );

      return [];

    }

    return $padData [$pad];

  }

  // The shared body of {chart} and {sparkline}: the options read, the rows gathered and
  // turned into points, and the level's data put back to its one default occurrence.

  function padChartTag ( $kind ) {

    global $pad, $padData;

    $sequence = padTagParm ( 'sequence' );
    $label    = (string) padTagParm ( 'label' );
    $value    = (string) padTagParm ( 'value' );
    $rows     = padChartRows ();

    $padData [$pad] = padDefaultData ();

    if ( $sequence !== '' and $sequence !== TRUE )
      $name = ucfirst ( (string) $sequence ) . ', the first ' . count ( $rows ) . ' terms';
    elseif ( $value !== '' )
      $name = ucfirst ( $value ) . ( $label !== '' ? " by $label" : '' );
    else
      $name = ucfirst ( $kind ) . ' chart';

    $wide = ( $kind == 'sparkline' );

    return padChart ( $kind, padChartPoints ( $rows, $label, $value ),
                      (string) padTagParm ( 'title', $name ),
                      max ( 20, (int) padTagParm ( 'width',  $wide ? 120 : 600 ) ),
                      max ( 10, (int) padTagParm ( 'height', $wide ? 32  : 300 ) ) );

  }

  function padChartPoints ( $rows, $label, $value ) {

    $points = [];
    $index  = 0;

    foreach ( (array) $rows as $key => $row ) {

      $index++;

      if ( is_object ( $row ) )
        $row = padToArray ( $row );

      if ( ! is_array ( $row ) ) {
        if ( padChartFinite ( $row ) )
          $points [] = [ is_string ( $key ) ? $key : (string) $index, $row + 0 ];
        continue;
      }

      $v = NULL;

      if ( $value !== '' )
        $v = $row [$value] ?? NULL;
      else
        foreach ( $row as $field )
          if ( is_numeric ( $field ) ) {
            $v = $field;
            break;
          }

      if ( ! padChartFinite ( $v ) )
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

      $points [] = [ is_scalar ( $l ) ? (string) $l : (string) $index, $v + 0 ];

    }

    return $points;

  }

  // A value the chart can plot: a number that is finite - '1e999' is numeric, but it is
  // INF as a float, and the axis made no ticks of it and ended the page with an undefined
  // array key - and no larger than 1e300 either way, so the axis around it stays finite
  // too: 1.7e308 rounded its top tick up to INF and the tick loop never ended, and 1e308
  // beside -1e308 spanned INF and divided by zero.

  function padChartFinite ( $value ) {

    return is_numeric ( $value ) and is_finite ( (float) $value ) and abs ( (float) $value ) <= 1e300;

  }

  // A number as an axis or a tooltip writes it: thousands grouped, at most two decimals.

  function padChartNumber ( $n ) {

    $decimals = ( abs ( $n - round ( $n ) ) < 1e-9 ) ? 0 : ( ( abs ( $n * 10 - round ( $n * 10 ) ) < 1e-9 ) ? 1 : 2 );

    return number_format ( $n, $decimals, '.', ',' );

  }

  // Round tick values, about $count of them, that take in min and max. A tick is rounded
  // to ten digits past its step, not ten decimals: values of 1e-11 made every tick 0, and
  // the plot divided by the span between the first and the last. The step is no smaller
  // than the smallest normal float, which a span of a denormal value would underflow.

  function padChartTicks ( $min, $max, $count = 5 ) {

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

    for ( $tick = $low; $tick <= $high + $step / 2; $tick += $step )
      $ticks [] = round ( $tick, $digits );

    return $ticks;

  }

  function padChartAttr ( $text ) {

    return htmlspecialchars ( (string) $text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );

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

  function padChartStyle () {

    $roles = [ 'series'  => [ '#2a78d6', '#3987e5' ],
               'text'    => [ '#52514e', '#c3c2b7' ],
               'grid'    => [ '#e4e3df', '#3a3a37' ],
               'surface' => [ '#fcfcfb', '#1a1a19' ] ];

    $light = $both = '';

    foreach ( $roles as $role => list ( $day, $night ) ) {
      $light .= "--pad-chart-$role:$day;";
      $both  .= "--pad-chart-$role:light-dark($day,$night);";
    }

    return '<style>'
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
         . '</style>';

  }

  // The SVG of one chart. $kind is bar, line or sparkline; $title names it, and the
  // points become its <desc>. The ids are made from the chart itself, so two charts on a
  // page do not share a title: a count per request gave a chart served from the fragment
  // cache and a chart rendered next to it the same pad-chart-1, and so did the charts of
  // two nested passes - two {example}s - each counting from one again. The same chart
  // drawn twice in a request is told apart by a number behind the second, kept in a
  // static for the nested passes.

  function padChart ( $kind, $points, $title, $width, $height ) {

    static $drawn = [];

    if ( ! $points )
      return '';

    $id = 'pad-chart-' . substr ( md5 ( serialize ( [ $kind, $points, $title, $width, $height ] ) ), 0, 8 );

    $drawn [$id] = ( $drawn [$id] ?? 0 ) + 1;

    if ( $drawn [$id] > 1 )
      $id .= '-' . $drawn [$id];

    $n    = count ( $points );
    $vals = array_column ( $points, 1 );
    $desc = [];

    foreach ( array_slice ( $points, 0, 60 ) as $point )
      $desc [] = $point [0] . ': ' . padChartNumber ( $point [1] );

    if ( $n > 60 )
      $desc [] = 'and ' . ( $n - 60 ) . ' more';

    $svg = '<svg xmlns="http://www.w3.org/2000/svg" class="pad-chart pad-chart-' . $kind . '" role="img"'
         . " aria-labelledby=\"$id-title $id-desc\" width=\"$width\" height=\"$height\" viewBox=\"0 0 $width $height\">"
         . "<title id=\"$id-title\">" . padChartAttr ( $title ) . '</title>'
         . "<desc id=\"$id-desc\">" . padChartAttr ( implode ( '; ', $desc ) ) . '</desc>'
         . padChartStyle ();

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

    // The category labels under the plot, thinned out when they would overlap.

    $labelWidth = 0;
    foreach ( $points as $point )
      $labelWidth = max ( $labelWidth, mb_strlen ( $point [0] ) * 6.5 + 8 );

    $every = max ( 1, (int) ceil ( $labelWidth / max ( 1, $band ) ) );

    foreach ( $points as $i => $point )
      if ( $i % $every == 0 )
        $svg .= '<text x="' . padChartXY ( $x ( $i ) ) . '" y="' . ( $height - 8 ) . '" text-anchor="middle">' . padChartAttr ( $point [0] ) . '</text>';

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

    if ( $max == $min ) {
      $max += 1;
      $min -= 1;
    }

    $x = fn ( $i ) => $n == 1 ? $width / 2 : $pad + $i * ( $width - 2 * $pad ) / ( $n - 1 );
    $y = fn ( $v ) => $pad + ( $max - $v ) / ( $max - $min ) * ( $height - 2 * $pad );

    $line = '';
    foreach ( $points as $i => $point )
      $line .= ( $i ? 'L' : 'M' ) . padChartXY ( $x ( $i ) ) . ',' . padChartXY ( $y ( $point [1] ) );

    return '<path class="pc-line" d="' . $line . '"/>'
         . '<circle class="pc-dot" cx="' . padChartXY ( $x ( $n - 1 ) ) . '" cy="' . padChartXY ( $y ( $points [$n - 1] [1] ) ) . '" r="3"/>';

  }

?>
