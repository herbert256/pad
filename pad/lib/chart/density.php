<?php

  // How values spread, as a smooth shape - the density curve and the violin.
  //
  //   {chart 'density', data='trials', label='group', value='ms'}
  //   {chart 'violin', data='scores', label='class', value='score', bandwidth=4}
  //
  // value= names the numbers - else the first numeric field; label= groups them, all
  // values in one group without it. Each group is smoothed by a Gaussian kernel density
  // estimate: every value a small bell of width bandwidth= - else Silverman's rule of
  // thumb, 0.9 times the smaller of the standard deviation and the quartile distance over
  // 1.34, times the count to the power -1/5, per group. A density draws a curve per group
  // over one value axis - a light wash and its line, the median as a dashed line, a legend
  // for several groups; the height is density, so it is not labelled. A violin draws a
  // shape per group on a vertical value axis, the density mirrored around its middle and
  // cut at the lowest and highest value, all on one scale of density - with the quartiles
  // as a short thick bar inside and the median as a light dot.

  function padChartDensity ( $kind, $rows, $width, $height ) {

    global $padCheckSyntax;

    list ( $numbers ) = padChartGuess ( $rows );

    $used   = [];
    $field  = padChartPick ( 'value', $numbers, $used );
    $label  = (string) padTagParm ( 'label' );
    $band   = padTagParm ( 'bandwidth' );
    $groups = [];

    if ( $band !== '' and ( ! padChartFinite ( $band ) or $band <= 0 ) ) {
      if ( $padCheckSyntax )
        padError ( 'the bandwidth of a ' . $kind . ' chart is a number above 0' );
      $band = '';
    }

    foreach ( (array) $rows as $row ) {
      $row = padChartRow ( $row );
      $v   = $row [$field] ?? NULL;
      if ( padChartFinite ( $v ) )
        $groups [ $label !== '' ? padChartText ( $row, $label ) : ucfirst ( $field ) ] [] = $v + 0;
    }

    if ( ! $groups )
      return '';

    // Per group: the sorted values, the bandwidth and the five numbers the tooltips and
    // the violin's bar tell.

    $sets = [];
    $desc = [];

    foreach ( $groups as $name => $values ) {

      sort ( $values );

      $n    = count ( $values );
      $mean = array_sum ( $values ) / $n;
      $sd   = sqrt ( array_sum ( array_map ( fn ( $v ) => ( $v - $mean ) ** 2, $values ) ) / max ( 1, $n - 1 ) );
      $q1   = padChartQuantile ( $values, 0.25 );
      $med  = padChartQuantile ( $values, 0.5 );
      $q3   = padChartQuantile ( $values, 0.75 );
      $iqr  = ( $q3 - $q1 ) / 1.34;
      $h    = ( $band !== '' ) ? $band + 0 : 0.9 * ( $iqr > 0 ? min ( $sd, $iqr ) : $sd ) * $n ** -0.2;

      // The fallbacks finite too: values near 1e300 overflow their squares, the standard
      // deviation is INF, and the bandwidth was that - the axis ran from -INF and the chart
      // ended on a cast.

      if ( ! ( $h > 0 ) or ! is_finite ( $h ) )
        $h = ( $sd > 0 and is_finite ( $sd ) ) ? $sd : ( ( end ( $values ) - $values [0] ) / 10 ?: max ( abs ( $mean ) * 0.1, 1e-9 ) );

      if ( ! ( $h > 0 ) or ! is_finite ( $h ) )
        $h = 1;

      $sets [$name] = [ $values, $h, $q1, $med, $q3 ];

      $desc [] = "$name: $n values, median " . padChartNumber ( $med ) . ', quartiles ' . padChartNumber ( $q1 ) . ' – ' . padChartNumber ( $q3 )
               . ', range ' . padChartNumber ( $values [0] ) . ' – ' . padChartNumber ( end ( $values ) );

    }

    $count = array_sum ( array_map ( 'count', $groups ) );
    $title = (string) padTagParm ( 'title', ucfirst ( $field !== '' ? $field : 'value' ) . ( $label !== '' ? " by $label" : ", $count values" ) );

    list ( $id, $svg ) = padChartOpen ( $kind, $sets, $desc, $title, $width, $height );

    $kde = function ( $values, $h, $x ) {
      $sum = 0;
      foreach ( $values as $v )
        $sum += exp ( -0.5 * ( ( $x - $v ) / $h ) ** 2 );
      return $sum / ( count ( $values ) * $h * sqrt ( 2 * M_PI ) );
    };

    if ( $kind == 'violin' )
      return padChartViolin ( $svg, $sets, $desc, $kde, $width, $height );

    // The value axis along the bottom, wide enough for the tails of every curve.

    $lo = $hi = NULL;

    foreach ( $sets as list ( $values, $h ) ) {
      $lo = min ( $lo ?? INF, $values [0] - 2.5 * $h );
      $hi = max ( $hi ?? -INF, end ( $values ) + 2.5 * $h );
    }

    $ticks   = padChartTicks ( $lo, $hi, max ( 3, min ( 8, (int) ( $width / 90 ) ) ) );
    $low     = $ticks [0];
    $high    = end ( $ticks );
    $legendH = 0;

    if ( count ( $sets ) > 1 ) {
      list ( $legend, $legendH ) = padChartLegend ( array_keys ( $sets ), 12, 6, $width - 24 );
      $svg .= $legend;
    }

    $left   = 14;
    $right  = 14;
    $top    = $legendH + 14;
    $bottom = 24;
    $plotW  = $width - $left - $right;
    $plotH  = max ( 10, $height - $top - $bottom );
    $x      = fn ( $v ) => $left + ( $v - $low ) / ( $high - $low ) * $plotW;
    $steps  = max ( 40, min ( 240, (int) ( $plotW / 3 ) ) );
    $curves = [];
    $peak   = 0;

    foreach ( $sets as $name => list ( $values, $h ) ) {

      $points = [];
      for ( $s = 0; $s <= $steps; $s++ )
        $points [] = $low + ( $high - $low ) * $s / $steps;

      // A bell narrower than the steps falls between them - one outlier stretches the axis
      // far beyond its bandwidth: the curve is read at the middle value of each pixel's
      // values too, and a bandwidth to either side of it.

      if ( ( $high - $low ) / $steps > $h / 2 ) {
        $pixels = [];
        foreach ( $values as $v )
          $pixels [ (int) floor ( ( $v - $low ) / ( $high - $low ) * $plotW ) ] [] = $v;
        foreach ( $pixels as $in ) {
          $v = $in [ intdiv ( count ( $in ), 2 ) ];
          array_push ( $points, $v - $h, $v, $v + $h );
        }
        sort ( $points );
      }

      foreach ( $points as $at ) {
        $curves [$name] [] = [ $at, $d = $kde ( $values, $h, $at ) ];
        $peak = max ( $peak, $d );
      }

    }

    $base = $top + $plotH;
    $y    = fn ( $d ) => $base - $d / ( ( $peak ?: 1 ) * 1.08 ) * $plotH;

    foreach ( $ticks as $tick )
      $svg .= '<line class="pc-grid" x1="' . padChartXY ( $x ( $tick ) ) . '" x2="' . padChartXY ( $x ( $tick ) ) . '" y1="' . padChartXY ( $top ) . '" y2="' . padChartXY ( $base ) . '"/>'
            . '<text x="' . padChartXY ( $x ( $tick ) ) . '" y="' . ( $height - 8 ) . '" text-anchor="middle">' . padChartNumber ( $tick ) . '</text>';

    // A curve per group: the wash with the group's tooltip, the line over it, and the
    // median dashed from the baseline up to the curve.

    $i = 0;

    foreach ( $curves as $name => $points ) {

      // Past eight groups the grey of 'Other', as the fill and the legend have it: the
      // curves were drawn in the eighth group's colour.

      $slot   = padChartSlot ( count ( $sets ) > 1 ? $i : 0 );
      $stroke = ( $i < 8 ) ? 'pc-l' . ( $i + 1 ) : 'pc-edge';
      $line   = '';

      foreach ( $points as $s => list ( $at, $d ) )
        $line .= ( $s ? 'L' : 'M' ) . padChartXY ( $x ( $at ) ) . ',' . padChartXY ( $y ( $d ) );

      $med  = $sets [$name] [3];
      $svg .= '<path class="pc-mark ' . $slot . '" fill-opacity="0.18" d="' . $line . 'L' . padChartXY ( $x ( $high ) ) . ',' . padChartXY ( $base ) . 'L' . padChartXY ( $x ( $low ) ) . ',' . padChartXY ( $base ) . 'Z">'
            . '<title>' . padChartAttr ( $desc [$i] ) . '</title></path>'
            . '<path class="' . $stroke . '" d="' . $line . '"/>'
            . '<line class="' . $stroke . '" style="stroke-width:1.5" stroke-dasharray="4 3" x1="' . padChartXY ( $x ( $med ) ) . '" x2="' . padChartXY ( $x ( $med ) ) . '"'
            . ' y1="' . padChartXY ( $base ) . '" y2="' . padChartXY ( $y ( $kde ( $sets [$name] [0], $sets [$name] [1], $med ) ) ) . '">'
            . '<title>' . padChartAttr ( "$name: median " . padChartNumber ( $med ) ) . '</title></line>';

      $i++;

    }

    $svg .= '<line class="pc-axis" x1="' . $left . '" x2="' . ( $width - $right ) . '" y1="' . padChartXY ( $base ) . '" y2="' . padChartXY ( $base ) . '"/>';

    return "$svg</svg>";

  }

  // The violins: a band per group along the bottom, the values up the left as a box plot
  // has them. Every shape on one scale of density, the widest half as wide as 45% of a
  // band; the density is read at about every 2px of the axis between the group's lowest
  // and highest value.

  function padChartViolin ( $svg, $sets, $desc, $kde, $width, $height ) {

    $all = [];
    foreach ( $sets as list ( $values ) )
      $all = array_merge ( $all, $values );

    list ( $svg, $left, $y ) = padChartValueAxis ( $svg, padChartTicks ( min ( $all ), max ( $all ), $height < 200 ? 3 : 5 ), $width, $height, 10, 24 );

    $n      = count ( $sets );
    $band   = ( $width - $left - 10 ) / $n;
    $half   = max ( 4, min ( 80, $band * 0.45 ) );
    $shapes = [];
    $peak   = 0;

    foreach ( $sets as $name => list ( $values, $h ) ) {
      $from  = $values [0];
      $to    = end ( $values );
      $steps = max ( 2, min ( 200, (int) ( abs ( $y ( $from ) - $y ( $to ) ) / 2 ) ) );
      for ( $s = 0; $s <= $steps; $s++ ) {
        $at = $from + ( $to - $from ) * $s / $steps;
        $shapes [$name] [] = [ $at, $d = $kde ( $values, $h, $at ) ];
        $peak = max ( $peak, $d );
      }
    }

    $i = 0;

    foreach ( $shapes as $name => $points ) {

      list ( $values, $h, $q1, $med, $q3 ) = $sets [$name];

      $c    = $left + ( $i + 0.5 ) * $band;
      $w    = fn ( $d ) => $d / $peak * $half;
      $slot = padChartSlot ( $n > 1 ? $i : 0 );
      $out  = $back = '';

      foreach ( $points as $s => list ( $at, $d ) ) {
        $out  .= ( $s ? 'L' : 'M' ) . padChartXY ( $c + $w ( $d ) ) . ',' . padChartXY ( $y ( $at ) );
        $back  = 'L' . padChartXY ( $c - $w ( $d ) ) . ',' . padChartXY ( $y ( $at ) ) . $back;
      }

      $line = ( $n > 1 and $i >= 8 ) ? 'pc-edge' : 'pc-l' . ( ( $n > 1 ? $i : 0 ) + 1 );
      $bar  = max ( 3, min ( 8, $half * 0.16 ) );

      $svg .= '<path class="pc-mark ' . $slot . '" fill-opacity="0.35" d="' . $out . $back . 'Z"><title>' . padChartAttr ( $desc [$i] ) . '</title></path>'
            . '<path class="' . $line . '" style="stroke-width:1.5" d="' . $out . $back . 'Z"/>'
            . '<line class="pc-axis" style="stroke-width:1.5" x1="' . padChartXY ( $c ) . '" x2="' . padChartXY ( $c ) . '" y1="' . padChartXY ( $y ( $values [0] ) ) . '" y2="' . padChartXY ( $y ( end ( $values ) ) ) . '"/>'
            . '<line class="pc-axis" style="stroke-width:' . padChartXY ( $bar ) . '" x1="' . padChartXY ( $c ) . '" x2="' . padChartXY ( $c ) . '" y1="' . padChartXY ( $y ( $q1 ) ) . '" y2="' . padChartXY ( $y ( $q3 ) ) . '">'
            . '<title>' . padChartAttr ( "$name: quartiles " . padChartNumber ( $q1 ) . ' – ' . padChartNumber ( $q3 ) ) . '</title></line>'
            . '<circle class="pc-in" cx="' . padChartXY ( $c ) . '" cy="' . padChartXY ( $y ( $med ) ) . '" r="' . padChartXY ( max ( 2, $bar * 0.45 ) ) . '">'
            . '<title>' . padChartAttr ( "$name: median " . padChartNumber ( $med ) ) . '</title></circle>'
            . '<text x="' . padChartXY ( $c ) . '" y="' . ( $height - 8 ) . '" text-anchor="middle">' . padChartAttr ( padChartFit ( $name, $band - 4 ) ) . '</text>';

      $i++;

    }

    return "$svg</svg>";

  }

?>
