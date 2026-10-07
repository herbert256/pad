<?php

  // How two numbers relate: a dot per row on two numeric axes.
  //
  //   {chart 'scatter', data='cars', x='weight', y='mpg', label='model'}
  //   {chart 'scatter', data='cars', x='weight', y='mpg', color='origin', trend}
  //   {chart 'bubble',  data='countries', x='gdp', y='life', size='population', label='name'}
  //
  // x= and y= name the numbers - else the first two numeric fields - and a bubble's size=
  // the third; its area grows with the value. label= names a dot in its tooltip, color=
  // colours the dots by a field, eight groups at most and the rest grey. trend draws the
  // least-squares line through every dot. Rows of single values plot each value against
  // its row number.

  function padChartScatter ( $kind, $rows, $seqName, $width, $height ) {

    list ( $numbers, $names ) = padChartGuess ( $rows );

    $used   = [];
    $xField = padChartPick ( 'x', $numbers, $used );
    $yField = padChartPick ( 'y', $numbers, $used );
    $sField = ( $kind == 'bubble' ) ? padChartPick ( 'size', $numbers, $used ) : '';
    $label  = (string) padTagParm ( 'label' );
    $group  = (string) padTagParm ( 'color' );
    $trend  = padTagParm ( 'trend', FALSE );
    $trend  = ( $trend !== FALSE and $trend !== '' and $trend !== 0 and $trend !== '0' );
    $alone  = ( $yField === '' );

    if ( $alone ) {
      $yField = $xField;
      $xField = '';
    }

    $title = (string) padTagParm ( 'title', $seqName !== '' ? $seqName
                                           : ( $yField !== '' ? ucfirst ( $yField ) . ( $xField !== '' ? " against $xField" : '' ) : ucfirst ( $kind ) . ' chart' ) );

    $dots   = [];
    $groups = [];
    $index  = 0;

    foreach ( (array) $rows as $row ) {

      $index++;
      $row = padChartRow ( $row );

      $x = ( $xField === '' ) ? $index : ( $row [$xField] ?? NULL );
      $y = $row [$yField] ?? NULL;
      $s = ( $sField === '' ) ? 1 : ( $row [$sField] ?? NULL );

      if ( ! padChartFinite ( $x ) or ! padChartFinite ( $y ) or ! padChartFinite ( $s ) or $s < 0 )
        continue;

      $g = padChartText ( $row, $group );

      if ( $group !== '' and ! isset ( $groups [$g] ) )
        $groups [$g] = count ( $groups );

      $dots [] = [ $x + 0, $y + 0, $s + 0, padChartText ( $row, $label ), $g ];

    }

    if ( ! $dots )
      return '';

    $xName = ( $xField === '' ) ? 'row' : $xField;
    $tip   = function ( $dot ) use ( $xName, $yField, $sField ) {
      return ( $dot [3] !== '' ? $dot [3] . ': ' : '' )
           . "$xName " . padChartNumber ( $dot [0] ) . ", $yField " . padChartNumber ( $dot [1] )
           . ( $sField !== '' ? ", $sField " . padChartNumber ( $dot [2] ) : '' );
    };

    list ( $id, $svg ) = padChartOpen ( $kind, $dots, array_map ( $tip, $dots ), $title, $width, $height );

    $xs = array_column ( $dots, 0 );
    $ys = array_column ( $dots, 1 );

    if ( ! is_finite ( max ( $xs ) - min ( $xs ) ) or ! is_finite ( max ( $ys ) - min ( $ys ) ) )
      return '';

    // A bubble reaches past its centre, so its axes take in a tenth more on each side.

    $xPad = ( $sField !== '' ) ? ( max ( $xs ) - min ( $xs ) ) / 10 : 0;
    $yPad = ( $sField !== '' ) ? ( max ( $ys ) - min ( $ys ) ) / 10 : 0;

    $xTicks = padChartTicks ( min ( $xs ) - $xPad, max ( $xs ) + $xPad, $width  < 300 ? 3 : 5 );
    $yTicks = padChartTicks ( min ( $ys ) - $yPad, max ( $ys ) + $yPad, $height < 200 ? 3 : 5 );
    $xLow   = $xTicks [0];  $xHigh = end ( $xTicks );
    $yLow   = $yTicks [0];  $yHigh = end ( $yTicks );

    $tickWidth = 0;
    foreach ( $yTicks as $tick )
      $tickWidth = max ( $tickWidth, padChartWidth ( padChartNumber ( $tick ) ) );

    // The group names - eight colours, the rest 'Other' - in a legend above the plot.

    $folded = ( count ( $groups ) > 8 );
    $keys   = array_keys ( $groups );

    if ( $folded )
      $keys = array_merge ( array_slice ( $keys, 0, 8 ), [ 'Other' ] );

    $left  = max ( 24, (int) ceil ( $tickWidth ) + 10 );
    $right = (int) max ( 10, ceil ( padChartWidth ( padChartNumber ( $xHigh ) ) / 2 ) + 4 );
    $top   = 6;

    if ( $groups ) {
      list ( $legend, $legendHeight ) = padChartLegend ( $keys, $left, $top, $width - $left - $right );
      $svg .= $legend;
      $top += $legendHeight;
    }

    $top   += 16;
    $bottom = 38;
    $plotW  = max ( 1, $width  - $left - $right );
    $plotH  = max ( 1, $height - $top  - $bottom );

    $px = fn ( $v ) => $left + ( $v - $xLow ) / ( $xHigh - $xLow ) * $plotW;
    $py = fn ( $v ) => $top  + ( $yHigh - $v ) / ( $yHigh - $yLow ) * $plotH;

    foreach ( $yTicks as $tick )
      $svg .= "<line class=\"pc-grid\" x1=\"$left\" x2=\"" . ( $left + $plotW ) . '" y1="' . padChartXY ( $py ( $tick ) ) . '" y2="' . padChartXY ( $py ( $tick ) ) . '"/>'
            . '<text x="' . ( $left - 6 ) . '" y="' . padChartXY ( $py ( $tick ) + 4 ) . '" text-anchor="end">' . padChartNumber ( $tick ) . '</text>';

    foreach ( $xTicks as $tick )
      $svg .= '<line class="pc-grid" x1="' . padChartXY ( $px ( $tick ) ) . '" x2="' . padChartXY ( $px ( $tick ) ) . "\" y1=\"$top\" y2=\"" . ( $top + $plotH ) . '"/>'
            . '<text x="' . padChartXY ( $px ( $tick ) ) . '" y="' . ( $top + $plotH + 16 ) . '" text-anchor="middle">' . padChartNumber ( $tick ) . '</text>';

    $svg .= '<text x="' . padChartXY ( $left + $plotW / 2 ) . '" y="' . ( $height - 6 ) . '" text-anchor="middle">' . padChartAttr ( $xName ) . '</text>'
          . '<text x="4" y="' . ( $top - 8 ) . '">' . padChartAttr ( $yField ) . '</text>'
          . "<line class=\"pc-axis\" x1=\"$left\" x2=\"" . ( $left + $plotW ) . '" y1="' . ( $top + $plotH ) . '" y2="' . ( $top + $plotH ) . '"/>';

    // The dots: 4px, or a bubble whose area is its size, the largest drawn first so the
    // small ones stay on top; each ringed by the surface where it overlaps another.

    $sMax  = max ( array_column ( $dots, 2 ) );
    $rMax  = max ( 6, min ( $plotW, $plotH ) / 10 );
    $order = array_keys ( $dots );

    if ( $sField !== '' )
      usort ( $order, fn ( $a, $b ) => $dots [$b] [2] <=> $dots [$a] [2] );

    foreach ( $order as $i ) {

      $dot  = $dots [$i];
      $r    = ( $sField === '' ) ? 4 : max ( 2, ( $sMax > 0 ? sqrt ( $dot [2] / $sMax ) : 0 ) * $rMax );
      $slot = ( $group === '' ) ? 'pc-c1' : ( ( $folded and $groups [$dot [4]] >= 8 ) ? 'pc-co' : padChartSlot ( $groups [$dot [4]] ) );

      $svg .= '<circle class="pc-mark pc-ring ' . $slot . ( $sField !== '' ? ' pc-bubble' : '' ) . '" cx="' . padChartXY ( $px ( $dot [0] ) ) . '" cy="' . padChartXY ( $py ( $dot [1] ) ) . '" r="' . padChartXY ( $r ) . '"><title>'
            . padChartAttr ( ( $group !== '' ? $dot [4] . ' · ' : '' ) . $tip ( $dot ) ) . '</title></circle>';

    }

    if ( $trend )
      $svg .= padChartTrend ( $dots, $xLow, $xHigh, $yLow, $yHigh, $px, $py );

    return "$svg</svg>";

  }

  // The least-squares line through the dots, cut to the plot: from where it enters the
  // value range to where it leaves it. Nothing when x does not vary.

  function padChartTrend ( $dots, $xLow, $xHigh, $yLow, $yHigh, $px, $py ) {

    $n = count ( $dots );
    $sx = $sy = $sxx = $sxy = 0;

    foreach ( $dots as $dot ) {
      $sx  += $dot [0];
      $sy  += $dot [1];
      $sxx += $dot [0] * $dot [0];
      $sxy += $dot [0] * $dot [1];
    }

    $denominator = $n * $sxx - $sx * $sx;

    if ( $n < 2 or ! is_finite ( $denominator ) or abs ( $denominator ) < 1e-300 )
      return '';

    $slope     = ( $n * $sxy - $sx * $sy ) / $denominator;
    $intercept = ( $sy - $slope * $sx ) / $n;

    if ( ! is_finite ( $slope ) or ! is_finite ( $intercept ) )
      return '';

    $from = $xLow;
    $to   = $xHigh;

    if ( $slope != 0 ) {
      $a    = ( $yLow  - $intercept ) / $slope;
      $b    = ( $yHigh - $intercept ) / $slope;
      $from = max ( $from, min ( $a, $b ) );
      $to   = min ( $to,   max ( $a, $b ) );
    }

    if ( $from >= $to )
      return '';

    $at = fn ( $x ) => padChartXY ( $px ( $x ) ) . ',' . padChartXY ( $py ( $slope * $x + $intercept ) );

    return '<path class="pc-trend" d="M' . $at ( $from ) . 'L' . $at ( $to ) . '"><title>Trend line</title></path>';

  }

?>
