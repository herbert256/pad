<?php

  // How values spread, group under group: a ridgeline - a density curve per group, each on a
  // baseline of its own, the groups stacked from the top down so their shapes are compared
  // along one value axis, a ridge rising over the one behind it.
  //
  //   {chart:ridgeline data='temperatures', label='month', value='celsius'}
  //   {chart:ridgeline data='trials', label='group', value='ms', bandwidth=10, overlap=2}
  //
  // value= names the numbers - else the first numeric field; label= the groups, in the order
  // they first appear, the first at the top - one ridge without it. Every group is smoothed
  // by a Gaussian kernel of one bandwidth, so the ridges are smoothed alike: bandwidth=, else
  // Silverman's rule of thumb over all the values together - 0.9 times the smaller of their
  // standard deviation and their quartile distance over 1.34, times their count to the power
  // -1/5 - which the <desc> names. Every ridge is a density - its area one, whatever the
  // number of values behind it - and all are on one scale, so a group of ten values and one of
  // a thousand are compared by their shape, never by their counts; the <desc> and each
  // ridge's tooltip say how many values it has. The tallest point of all rises overlap= bands
  // (1.5) above its baseline - 1 just touches the baseline above, more lets the ridges overlap
  // further. The median is a light line from the baseline up to the curve. A row whose value
  // is no number is left out.

  function padChartRidgeline ( $rows, $width, $height ) {

    global $padCheckSyntax;

    list ( $numbers ) = padChartGuess ( $rows );

    $used    = [];
    $field   = padChartPick ( 'value', $numbers, $used );
    $label   = (string) padTagParm ( 'label' );
    $band    = padTagParm ( 'bandwidth' );
    $overlap = padTagParm ( 'overlap' );

    if ( $field === '' ) {
      if ( $padCheckSyntax and $rows )
        padError ( "a ridgeline chart needs a numeric field - value='field'" );
      return '';
    }

    if ( $padCheckSyntax and $rows and ! padChartFieldSomewhere ( $rows, $field ) )
      padError ( "the ridgeline chart's value='" . padMakeSafe ( $field, 30 ) . "' is no field of its rows" );

    // A bandwidth below 1e-300, the bound of the values themselves (padChartFinite), is none:
    // 1e-310 passed, the kernel's 1 / ( n h sqrt ( 2 pi ) ) overflowed and the ridge's path
    // was written with nan - and a bandwidth the rule makes that small falls back as one of 0.

    if ( $band !== '' and ( ! padChartFinite ( $band ) or $band < 1e-300 ) ) {
      if ( $padCheckSyntax )
        padError ( 'the bandwidth of a ridgeline chart is a number above 0, 1e-300 at the least' );
      $band = '';
    }

    if ( $overlap !== '' and ( ! padChartFinite ( $overlap ) or $overlap <= 0 or $overlap > 10 ) ) {
      if ( $padCheckSyntax )
        padError ( 'the overlap of a ridgeline chart is a number above 0 and up to 10 - the bands its tallest ridge rises' );
      $overlap = '';
    }

    $overlap = ( $overlap !== '' ) ? $overlap + 0 : 1.5;
    $groups  = [];

    foreach ( (array) $rows as $row ) {
      $row = padChartRow ( $row );
      $v   = $row [$field] ?? NULL;
      if ( padChartFinite ( $v ) )
        $groups [ $label !== '' ? padChartText ( $row, $label ) : ucfirst ( $field ) ] [] = $v + 0;
    }

    if ( ! $groups )
      return '';

    // One bandwidth for every ridge, from all the values together, with the fallbacks of the
    // density curve when the rule gives none.

    $all = [];
    foreach ( $groups as $values )
      array_push ( $all, ...$values );

    sort ( $all );

    $count = count ( $all );
    $mean  = array_sum ( $all ) / $count;
    $sd    = sqrt ( array_sum ( array_map ( fn ( $v ) => ( $v - $mean ) ** 2, $all ) ) / max ( 1, $count - 1 ) );
    $iqr   = ( padChartQuantile ( $all, 0.75 ) - padChartQuantile ( $all, 0.25 ) ) / 1.34;
    $h     = ( $band !== '' ) ? $band + 0 : 0.9 * ( $iqr > 0 ? min ( $sd, $iqr ) : $sd ) * $count ** -0.2;

    if ( ! ( $h >= 1e-300 ) or ! is_finite ( $h ) )
      $h = ( $sd >= 1e-300 and is_finite ( $sd ) ) ? $sd : ( ( end ( $all ) - $all [0] ) / 10 ?: max ( abs ( $mean ) * 0.1, 1e-9 ) );

    if ( ! ( $h >= 1e-300 ) or ! is_finite ( $h ) )
      $h = 1;

    $sets = [];
    $desc = [];

    foreach ( $groups as $name => $values ) {

      sort ( $values );

      $n   = count ( $values );
      $med = padChartQuantile ( $values, 0.5 );

      $sets [$name] = [ $values, $med ];

      $desc [] = "$name: $n " . ( $n == 1 ? 'value' : 'values' ) . ', median ' . padChartNumber ( $med )
               . ', quartiles ' . padChartNumber ( padChartQuantile ( $values, 0.25 ) ) . ' – ' . padChartNumber ( padChartQuantile ( $values, 0.75 ) )
               . ', range ' . padChartNumber ( $values [0] ) . ' – ' . padChartNumber ( end ( $values ) );

    }

    $desc [] = 'one bandwidth for every ridge, ' . padChartNumber ( $h );

    $title = (string) padTagParm ( 'title', ucfirst ( $field ) . ( $label !== '' ? " by $label" : ", $count values" ) );

    list ( $id, $svg ) = padChartOpen ( 'ridgeline', [ $sets, $h, $overlap ], $desc, $title, $width, $height );

    // The names on the left, at most a third of the width; the value axis along the bottom,
    // wide enough for the tails of every ridge.

    $labelW = 0;
    foreach ( array_keys ( $sets ) as $name )
      $labelW = max ( $labelW, padChartWidth ( $name ) );

    $n      = count ( $sets );
    $left   = 12 + min ( $labelW, $width / 3 ) + 10;
    $ticks  = padChartTicks ( $all [0] - 2.5 * $h, end ( $all ) + 2.5 * $h, max ( 3, min ( 8, (int) ( ( $width - $left ) / 90 ) ) ) );
    $low    = $ticks [0];
    $high   = end ( $ticks );
    $right  = max ( 14, (int) ceil ( padChartWidth ( padChartNumber ( $high ) ) / 2 ) + 4 );
    $top    = 8;
    $bottom = 24;
    $plotW  = max ( 10, $width - $left - $right );
    $plotH  = max ( 10, $height - $top - $bottom );
    $step   = $plotH / ( $n - 1 + $overlap );
    $x      = fn ( $v ) => $left + ( $v - $low ) / ( $high - $low ) * $plotW;
    $base   = fn ( $i ) => $top + ( $overlap + $i ) * $step;

    // Each ridge read at about every 3px of the axis - and where a bell narrower than that
    // would fall between the points, at the middle value of each pixel's values and a
    // bandwidth to either side of it, as the density curve does.

    $steps  = max ( 40, min ( 240, (int) ( $plotW / 3 ) ) );
    $curves = [];
    $peak   = 0;

    foreach ( $sets as $name => list ( $values ) ) {

      $points = [];
      for ( $s = 0; $s <= $steps; $s++ )
        $points [] = $low + ( $high - $low ) * $s / $steps;

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
        $curves [$name] [] = [ $at, $d = padChartKde ( $values, $h, $at ) ];
        $peak = max ( $peak, $d );
      }

    }

    $rise = fn ( $d ) => $d / ( $peak ?: 1 ) * $overlap * $step;

    foreach ( $ticks as $tick )
      $svg .= '<line class="pc-grid" x1="' . padChartXY ( $x ( $tick ) ) . '" x2="' . padChartXY ( $x ( $tick ) ) . "\" y1=\"$top\" y2=\"" . padChartXY ( $base ( $n - 1 ) ) . '"/>'
            . '<text x="' . padChartXY ( $x ( $tick ) ) . '" y="' . ( $height - 8 ) . '" text-anchor="middle">' . padChartNumber ( $tick ) . '</text>';

    // The ridges from the top down, each over the ones behind it: its baseline, the shape
    // with the surface around it and the group's tooltip, the median, and the name.

    $i = 0;

    foreach ( $curves as $name => $points ) {

      $b    = $base ( $i );
      $med  = $sets [$name] [1];
      $line = '';

      foreach ( $points as $s => list ( $at, $d ) )
        $line .= ( $s ? 'L' : 'M' ) . padChartXY ( $x ( $at ) ) . ',' . padChartXY ( $b - $rise ( $d ) );

      $svg .= "<line class=\"pc-grid\" x1=\"" . padChartXY ( $left ) . '" x2="' . padChartXY ( $left + $plotW ) . '" y1="' . padChartXY ( $b ) . '" y2="' . padChartXY ( $b ) . '"/>'
            . '<path class="pc-mark pc-c1 pc-gap" fill-opacity="0.85" d="' . $line . 'L' . padChartXY ( $x ( end ( $points ) [0] ) ) . ',' . padChartXY ( $b )
            . 'L' . padChartXY ( $x ( $points [0] [0] ) ) . ',' . padChartXY ( $b ) . 'Z"><title>' . padChartAttr ( $desc [$i] ) . '</title></path>'
            . '<line class="pc-ring" x1="' . padChartXY ( $x ( $med ) ) . '" x2="' . padChartXY ( $x ( $med ) ) . '" y1="' . padChartXY ( $b ) . '" y2="' . padChartXY ( $b - $rise ( padChartKde ( $sets [$name] [0], $h, $med ) ) ) . '">'
            . '<title>' . padChartAttr ( "$name: median " . padChartNumber ( $med ) ) . '</title></line>'
            . '<text x="' . padChartXY ( $left - 10 ) . '" y="' . padChartXY ( $b - 3 ) . '" text-anchor="end">' . padChartAttr ( padChartFit ( $name, $left - 22 ) ) . '</text>';

      $i++;

    }

    $svg .= '<line class="pc-axis" x1="' . padChartXY ( $left ) . '" x2="' . padChartXY ( $left + $plotW ) . '" y1="' . padChartXY ( $base ( $n - 1 ) ) . '" y2="' . padChartXY ( $base ( $n - 1 ) ) . '"/>';

    return "$svg</svg>";

  }

  // The Gaussian kernel density of values at $x, for the bandwidth $h - the kernel the
  // density curve and the violin read as well (density.php).

  function padChartKde ( $values, $h, $x ) {

    $sum = 0;

    foreach ( $values as $v )
      $sum += exp ( -0.5 * ( ( $x - $v ) / $h ) ** 2 );

    return $sum / ( count ( $values ) * $h * sqrt ( 2 * M_PI ) );

  }

?>
