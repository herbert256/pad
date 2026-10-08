<?php

  // Two measures of a different kind on one chart: bars on the left axis, a line on the
  // right one.
  //
  //   {chart 'dual', data='results', label='month', value='revenue', line='margin'}
  //
  // label= names the categories - else the first field that is no number; value= the
  // field drawn as bars, on the left axis - else the first numeric field; line= the field
  // drawn as a line, on the right axis - else the next numeric one. Each axis has its own
  // ticks, written in its series' colour; the right ticks fall on the grid lines of the
  // left ones whenever their zeros can line up. A legend above names both.

  function padChartDual ( $rows, $width, $height ) {

    global $padCheckSyntax;

    list ( $numbers, $names ) = padChartGuess ( $rows );

    $used  = [];
    $label = padChartPick ( 'label', $names,   $used );
    $value = padChartPick ( 'value', $numbers, $used );
    $line  = padChartPick ( 'line',  $numbers, $used );

    if ( $value === '' or $line === '' or $value === $line ) {
      if ( $padCheckSyntax and $rows )
        padError ( "a dual axis chart needs two numeric fields - value='bars' and line='line'" );
      return '';
    }

    list ( $labels, $series ) = padChartTable ( $rows, $label, [ $value, $line ] );

    $n = count ( $labels );

    if ( ! $n )
      return '';

    list ( $barName, $lineName ) = array_keys ( $series );
    list ( $bars,    $points   ) = array_values ( $series );

    $desc = [];
    foreach ( $labels as $i => $text )
      $desc [] = "$text: $barName " . padChartNumber ( $bars [$i] ) . ", $lineName " . padChartNumber ( $points [$i] );

    $title = (string) padTagParm ( 'title', "$barName and $lineName" . ( $label !== '' ? " by $label" : '' ) );

    list ( $id, $svg ) = padChartOpen ( 'dual', [ $labels, $series ], $desc, $title, $width, $height );

    // The left axis takes in zero, the base of the bars. The right one is fitted to the
    // same grid: a round step that puts as many intervals above and below its zero as the
    // left has - else, when the line's values do not fit that, ticks of its own.

    $count = $height < 200 ? 3 : 5;
    $left  = padChartTicks ( min ( 0, min ( $bars ) ), max ( 0, max ( $bars ) ), $count );

    if ( ! is_finite ( end ( $left ) - $left [0] ) )
      return '';

    $right = padChartDualTicks ( $left, min ( $points ), max ( $points ), $count );
    $aligned = ( count ( $right ) == count ( $left ) );

    $lowL  = $left [0];
    $highL = end ( $left );
    $lowR  = $right [0];
    $highR = end ( $right );

    $leftW = $rightW = 0;
    foreach ( $left as $tick )
      $leftW = max ( $leftW, padChartWidth ( padChartNumber ( $tick ) ) );
    foreach ( $right as $tick )
      $rightW = max ( $rightW, padChartWidth ( padChartNumber ( $tick ) ) );

    $marginL = max ( 24, (int) ceil ( $leftW )  + 10 );
    $marginR = max ( 24, (int) ceil ( $rightW ) + 10 );

    list ( $legend, $legendH ) = padChartLegend ( [ "$barName (left)", "$lineName (right)" ], $marginL, 6, $width - $marginL - $marginR );

    $svg   .= $legend;
    $top    = 10 + $legendH;
    $bottom = 24;
    $plotW  = max ( 1, $width  - $marginL - $marginR );
    $plotH  = max ( 1, $height - $top - $bottom );
    $band   = $plotW / $n;
    $mid    = fn ( $i ) => $marginL + ( $i + 0.5 ) * $band;
    $yL     = fn ( $v ) => $top + ( $highL - $v ) / ( $highL - $lowL ) * $plotH;
    $yR     = fn ( $v ) => $top + ( $highR - $v ) / ( $highR - $lowR ) * $plotH;

    foreach ( $left as $tick )
      $svg .= '<line class="pc-grid" x1="' . $marginL . '" x2="' . ( $width - $marginR ) . '" y1="' . padChartXY ( $yL ( $tick ) ) . '" y2="' . padChartXY ( $yL ( $tick ) ) . '"/>'
            . '<text class="pc-c1" x="' . ( $marginL - 6 ) . '" y="' . padChartXY ( $yL ( $tick ) + 4 ) . '" text-anchor="end">' . padChartNumber ( $tick ) . '</text>';

    foreach ( $right as $tick ) {
      if ( ! $aligned )
        $svg .= '<line class="pc-grid" stroke-dasharray="2 3" x1="' . $marginL . '" x2="' . ( $width - $marginR ) . '" y1="' . padChartXY ( $yR ( $tick ) ) . '" y2="' . padChartXY ( $yR ( $tick ) ) . '"/>';
      $svg .= '<text class="pc-c2" x="' . ( $width - $marginR + 6 ) . '" y="' . padChartXY ( $yR ( $tick ) + 4 ) . '">' . padChartNumber ( $tick ) . '</text>';
    }

    // The categories under the plot, thinned out when they would overlap.

    $labelW = 0;
    foreach ( $labels as $text )
      $labelW = max ( $labelW, padChartWidth ( $text ) + 8 );

    $every = max ( 1, (int) ceil ( $labelW / max ( 1, $band ) ) );

    foreach ( $labels as $i => $text )
      if ( $i % $every == 0 )
        $svg .= '<text x="' . padChartXY ( $mid ( $i ) ) . '" y="' . ( $height - 8 ) . '" text-anchor="middle">' . padChartAttr ( $text ) . '</text>';

    // The bars, then the line over them with a ringed point per category.

    $barW = max ( 1, min ( 28, $band * 0.6 ) );
    $base = $yL ( 0 );

    foreach ( $bars as $i => $v )
      $svg .= '<path class="pc-mark pc-c1" d="' . padChartBarPath ( $mid ( $i ) - $barW / 2, $mid ( $i ) + $barW / 2, $base, $yL ( $v ) ) . '">'
            . '<title>' . padChartAttr ( "{$labels [$i]} · $barName: " . padChartNumber ( $v ) ) . '</title></path>';

    $svg .= '<line class="pc-axis" x1="' . $marginL . '" x2="' . ( $width - $marginR ) . '" y1="' . padChartXY ( $base ) . '" y2="' . padChartXY ( $base ) . '"/>';

    $path = $dots = '';

    foreach ( $points as $i => $v ) {
      $path .= ( $i ? 'L' : 'M' ) . padChartXY ( $mid ( $i ) ) . ',' . padChartXY ( $yR ( $v ) );
      $dots .= '<circle class="pc-mark pc-c2 pc-ring" cx="' . padChartXY ( $mid ( $i ) ) . '" cy="' . padChartXY ( $yR ( $v ) ) . '" r="4">'
             . '<title>' . padChartAttr ( "{$labels [$i]} · $lineName: " . padChartNumber ( $v ) ) . '</title></circle>';
    }

    $svg .= "<path class=\"pc-l2\" d=\"$path\"/>$dots";

    return "$svg</svg>";

  }

  // The ticks of the right axis on the grid of the left one: as many steps below zero and
  // above it as the left has, each a round number - 1, 2, 2.5 or 5 times a power of ten -
  // big enough to take in the line's values. When the left has no step on the side a value
  // of the line lies, its own ticks instead.

  function padChartDualTicks ( $left, $min, $max, $count ) {

    $stepL = $left [1] - $left [0];
    $below = (int) round ( - $left [0] / $stepL );
    $above = (int) round ( end ( $left ) / $stepL );

    if ( ( $min < 0 and $below == 0 ) or ( $max > 0 and $above == 0 ) or ( $min == 0 and $max == 0 ) )
      return padChartTicks ( min ( 0, $min ), max ( 0, $max ), $count );

    $need = max ( $max > 0 ? $max / $above : 0, $min < 0 ? - $min / $below : 0 );
    $step = 10 ** floor ( log10 ( $need ) );

    foreach ( [ 1, 2, 2.5, 5, 10 ] as $times )
      if ( $step * $times >= $need - 1e-12 ) {
        $step *= $times;
        break;
      }

    $digits = max ( 10, 10 - (int) floor ( log10 ( $step ) ) );
    $ticks  = [];

    for ( $k = - $below; $k <= $above; $k++ )
      $ticks [] = round ( $k * $step, $digits );

    return $ticks;

  }

?>
