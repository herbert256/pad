<?php

  // How a total is made up, step by step: each bar floats from where the one before ended.
  //
  //   {chart 'waterfall', data='result', label='step', value='amount', total='Profit'}
  //
  // A row is a step up (green) or down (red) of value= - else the first numeric field -
  // named by label=. A closing bar from zero to the end, in the series colour, is named by
  // total= ('Total'); total='' leaves it out. A dashed line carries each level to the next
  // bar, and a step with room above it shows its change.

  function padChartWaterfall ( $rows, $width, $height ) {

    $label  = (string) padTagParm ( 'label' );
    $value  = (string) padTagParm ( 'value' );
    $points = padChartPoints ( $rows, $label, $value );
    $total  = (string) padTagParm ( 'total', 'Total' );

    if ( ! $points )
      return '';

    $steps = [];
    $level = 0;

    foreach ( $points as list ( $name, $v ) ) {
      $steps [] = [ $name, $level, $level + $v, $v, $v >= 0 ? 'pc-c3' : 'pc-c8' ];
      $level   += $v;
    }

    if ( $total !== '' )
      $steps [] = [ $total, 0, $level, $level, 'pc-c1' ];

    if ( ! is_finite ( $level ) )
      return '';

    $sign = fn ( $v ) => ( $v > 0 ? '+' : '' ) . padChartNumber ( $v );
    $desc = [];

    foreach ( $steps as $i => list ( $name, $from, $to, $v ) )
      $desc [] = "$name: " . ( ( $total !== '' and $i == count ( $steps ) - 1 ) ? padChartNumber ( $v ) : $sign ( $v ) );

    $title = (string) padTagParm ( 'title', ucfirst ( $value !== '' ? $value : 'value' ) . ( $label !== '' ? " by $label" : '' ) );

    list ( $id, $svg ) = padChartOpen ( 'waterfall', $steps, $desc, $title, $width, $height );

    $all   = array_merge ( [ 0 ], array_column ( $steps, 1 ), array_column ( $steps, 2 ) );
    $ticks = padChartTicks ( min ( $all ), max ( $all ), $height < 200 ? 3 : 5 );
    $low   = $ticks [0];
    $high  = end ( $ticks );

    $tickW = 0;
    foreach ( $ticks as $tick )
      $tickW = max ( $tickW, padChartWidth ( padChartNumber ( $tick ) ) );

    $left   = max ( 24, (int) ceil ( $tickW ) + 10 );
    $right  = 10;
    $top    = 18;
    $bottom = 24;
    $plotW  = $width  - $left - $right;
    $plotH  = $height - $top  - $bottom;
    $n      = count ( $steps );
    $band   = $plotW / $n;
    $barW   = max ( 2, min ( 48, $band * 0.7 ) );
    $y      = fn ( $v ) => $top + ( $high - $v ) / ( $high - $low ) * $plotH;
    $mid    = fn ( $i ) => $left + ( $i + 0.5 ) * $band;

    foreach ( $ticks as $tick )
      $svg .= '<line class="pc-grid" x1="' . $left . '" x2="' . ( $width - $right ) . '" y1="' . padChartXY ( $y ( $tick ) ) . '" y2="' . padChartXY ( $y ( $tick ) ) . '"/>'
            . '<text x="' . ( $left - 6 ) . '" y="' . padChartXY ( $y ( $tick ) + 4 ) . '" text-anchor="end">' . padChartNumber ( $tick ) . '</text>';

    $labelW = 0;
    foreach ( $steps as $step )
      $labelW = max ( $labelW, padChartWidth ( $step [0] ) + 8 );

    $every = max ( 1, (int) ceil ( $labelW / max ( 1, $band ) ) );

    foreach ( $steps as $i => list ( $name, $from, $to, $v, $class ) ) {

      $x0 = $mid ( $i ) - $barW / 2;
      $y0 = $y ( max ( $from, $to ) );
      $h  = max ( 1, abs ( $y ( $from ) - $y ( $to ) ) );

      $svg .= '<rect class="pc-mark ' . $class . '" x="' . padChartXY ( $x0 ) . '" y="' . padChartXY ( $y0 ) . '" width="' . padChartXY ( $barW ) . '" height="' . padChartXY ( $h ) . '" rx="2">'
            . '<title>' . padChartAttr ( $desc [$i] ) . '</title></rect>';

      if ( $i < $n - 1 )
        $svg .= '<line class="pc-edge" stroke-dasharray="3 3" x1="' . padChartXY ( $x0 + $barW ) . '" x2="' . padChartXY ( $mid ( $i + 1 ) - $barW / 2 ) . '"'
              . ' y1="' . padChartXY ( $y ( $to ) ) . '" y2="' . padChartXY ( $y ( $to ) ) . '"/>';

      $text = ( $class == 'pc-c1' ) ? padChartNumber ( $v ) : $sign ( $v );

      if ( padChartWidth ( $text ) <= $band - 2 )
        $svg .= '<text x="' . padChartXY ( $mid ( $i ) ) . '" y="' . padChartXY ( $y0 - 4 ) . '" text-anchor="middle">' . padChartAttr ( $text ) . '</text>';

      if ( $i % $every == 0 )
        $svg .= '<text x="' . padChartXY ( $mid ( $i ) ) . '" y="' . ( $height - 8 ) . '" text-anchor="middle">' . padChartAttr ( padChartFit ( $name, $band * $every - 4 ) ) . '</text>';

    }

    $svg .= '<line class="pc-axis" x1="' . $left . '" x2="' . ( $width - $right ) . '" y1="' . padChartXY ( $y ( 0 ) ) . '" y2="' . padChartXY ( $y ( 0 ) ) . '"/>';

    return "$svg</svg>";

  }

?>
