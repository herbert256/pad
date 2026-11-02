<?php

  // Many points on two numeric axes, counted: a hexbin chart lays a grid of hexagons over the
  // plot and colours each by the number of points in it - where a scatter of thousands of
  // dots is one dark blot, the hexagons show where they crowd.
  //
  //   {chart:hexbin data='trips', x='distance', y='minutes'}
  //   {chart:hexbin data='stars', x='colour', y='brightness', bins=30}
  //
  // x= and y= name the numbers - else the first two numeric fields, a field the other names
  // left to it, so y= alone draws against the first other number. The axes take in every
  // point, round numbers without a zero of their own, as a scatter's do; bins= hexagons (20,
  // 2 to 100) fit across the plot, pointed at the top, every second row shifted by half a
  // hexagon. The grid lies on the axes, so a point's hexagon follows from its numbers alone:
  // the one whose middle is nearest - a point exactly between two goes to the upper row, then
  // to the left one. Every point is counted once, so the counts add up to the points drawn,
  // which the <desc> says first, with the hexagons and the most in one; a row without two
  // numbers is left out and counted there too. The colour is the count, in six steps of one
  // hue from one point to the most - every hexagon has the same area, so it reads as the
  // density as well - with the scale above the plot; each hexagon's tooltip says its count and
  // the middle it lies around. The hexagons at the edge are cut at the plot. The SVG grows with
  // the hexagons that hold a point, never with the rows.

  function padChartHexbin ( $rows, $width, $height ) {

    global $padCheckSyntax;

    list ( $numbers ) = padChartGuess ( $rows );

    // A field x= or y= names is no guess for the other: y= alone took that field for x as
    // well, and the chart failed for want of two fields (dumbbell does the same).

    $used   = array_values ( array_filter ( [ (string) padTagParm ( 'x' ), (string) padTagParm ( 'y' ) ], 'strlen' ) );
    $xField = padChartPick ( 'x', $numbers, $used );
    $yField = padChartPick ( 'y', $numbers, $used );
    $bins   = padTagParm ( 'bins', 20 );

    if ( $xField === '' or $yField === '' or $xField === $yField ) {
      if ( $padCheckSyntax and $rows )
        padError ( "a hexbin chart needs two numeric fields - x='field' and y='field'" );
      return '';
    }

    if ( $padCheckSyntax and $rows )
      foreach ( [ 'x' => $xField, 'y' => $yField ] as $option => $field )
        if ( ! padChartFieldSomewhere ( $rows, $field ) )
          padError ( "the hexbin chart's $option='" . padMakeSafe ( $field, 30 ) . "' is no field of its rows" );

    if ( ! padChartFinite ( $bins ) or $bins != floor ( $bins ) or $bins < 2 or $bins > 100 ) {
      if ( $padCheckSyntax )
        padError ( 'the bins of a hexbin chart are a whole number from 2 to 100 - the hexagons across the plot' );
      $bins = 20;
    }

    $bins   = (int) $bins;
    $points = [];
    $left   = 0;

    foreach ( (array) $rows as $row ) {

      $row = padChartRow ( $row );
      $x   = $row [$xField] ?? NULL;
      $y   = $row [$yField] ?? NULL;

      if ( padChartFinite ( $x ) and padChartFinite ( $y ) )
        $points [] = [ $x + 0, $y + 0 ];
      else
        $left++;

    }

    if ( ! $points )
      return '';

    $xs = array_column ( $points, 0 );
    $ys = array_column ( $points, 1 );

    if ( ! is_finite ( max ( $xs ) - min ( $xs ) ) or ! is_finite ( max ( $ys ) - min ( $ys ) ) )
      return '';

    $xTicks = padChartTicks ( min ( $xs ), max ( $xs ), $width  < 300 ? 3 : 5 );
    $yTicks = padChartTicks ( min ( $ys ), max ( $ys ), $height < 200 ? 3 : 5 );
    $xLow   = $xTicks [0];  $xHigh = end ( $xTicks );
    $yLow   = $yTicks [0];  $yHigh = end ( $yTicks );

    $tickWidth = 0;
    foreach ( $yTicks as $tick )
      $tickWidth = max ( $tickWidth, padChartWidth ( padChartNumber ( $tick ) ) );

    $margin = max ( 24, (int) ceil ( $tickWidth ) + 10 );
    $right  = (int) max ( 10, ceil ( padChartWidth ( padChartNumber ( $xHigh ) ) / 2 ) + 4 );
    $top    = 38;
    $bottom = 38;
    $plotW  = max ( 1, $width  - $margin - $right );
    $plotH  = max ( 1, $height - $top - $bottom );

    // The grid: bins hexagons across, of radius r; a row every 1.5 r, every second one half a
    // hexagon to the right. A point's hexagon is the nearest middle of the two rows around
    // it - the one it lies in is one of those - and of the two columns around it in each.

    $r  = $plotW / ( $bins * sqrt ( 3 ) );
    $dx = $r * sqrt ( 3 );
    $dy = $r * 1.5;

    $cells = [];

    foreach ( $points as list ( $x, $y ) ) {

      $px   = ( $x - $xLow ) / ( $xHigh - $xLow ) * $plotW;
      $py   = ( $yHigh - $y ) / ( $yHigh - $yLow ) * $plotH;
      $j0   = (int) floor ( $py / $dy );
      $best = NULL;

      for ( $j = $j0; $j <= $j0 + 1; $j++ ) {
        $shift = ( $j % 2 ) ? $dx / 2 : 0;
        $i0    = (int) floor ( ( $px - $shift ) / $dx );
        for ( $i = $i0; $i <= $i0 + 1; $i++ ) {
          $ex = $i * $dx + $shift - $px;
          $ey = $j * $dy - $py;
          $d  = $ex * $ex + $ey * $ey;
          if ( $best === NULL or $d < $best [0] )
            $best = [ $d, $i, $j ];
        }
      }

      $key = $best [2] * 1000 + $best [1] + 1;
      $cells [$key] = ( $cells [$key] ?? 0 ) + 1;

    }

    ksort ( $cells );

    // A hexagon's middle in the numbers of the axes, for its tooltip.

    $middle = function ( $key ) use ( $dx, $dy, $plotW, $plotH, $xLow, $xHigh, $yLow, $yHigh ) {
      $j = intdiv ( $key, 1000 );
      $i = $key % 1000 - 1;
      $cx = $i * $dx + ( ( $j % 2 ) ? $dx / 2 : 0 );
      $cy = $j * $dy;
      return [ $cx, $cy, $xLow + $cx / $plotW * ( $xHigh - $xLow ), $yHigh - $cy / $plotH * ( $yHigh - $yLow ) ];
    };

    $total = count ( $points );
    $most  = max ( $cells );
    $tip   = function ( $key, $count ) use ( $middle, $xField, $yField ) {
      list ( , , $x, $y ) = $middle ( $key );
      return padChartNumber ( $count ) . ( $count == 1 ? ' point' : ' points' ) . " around $xField " . padChartNumber ( round ( $x, 2 ) ) . ", $yField " . padChartNumber ( round ( $y, 2 ) );
    };

    $order = array_keys ( $cells );
    usort ( $order, fn ( $a, $b ) => $cells [$b] <=> $cells [$a] );

    $desc = [ padChartNumber ( $total ) . ( $total == 1 ? ' point' : ' points' ) . ' in ' . padChartNumber ( count ( $cells ) ) . ( count ( $cells ) == 1 ? ' hexagon' : ' hexagons' )
              . ', at most ' . padChartNumber ( $most ) . ' in one' . ( $left ? ', ' . padChartNumber ( $left ) . ( $left == 1 ? ' row' : ' rows' ) . ' without two numbers' : '' ) ];

    foreach ( $order as $key )
      $desc [] = $tip ( $key, $cells [$key] );

    $title = (string) padTagParm ( 'title', ucfirst ( $yField ) . " against $xField, " . padChartNumber ( $total ) . ( $total == 1 ? ' point' : ' points' ) );

    list ( $id, $svg ) = padChartOpen ( 'hexbin', [ $cells, $bins ], $desc, $title, $width, $height );

    // The scale above the plot: one point, the six steps, the most in one.

    $svg .= '<text x="' . $margin . '" y="15">1</text>';

    $at = $margin + padChartWidth ( '1' ) + 6;

    for ( $step = 1; $step <= 6; $step++ )
      $svg .= '<rect class="pc-h' . $step . '" x="' . padChartXY ( $at + ( $step - 1 ) * 16 ) . '" y="6" width="16" height="10"/>';

    $svg .= '<text x="' . padChartXY ( $at + 6 * 16 + 6 ) . '" y="15">' . padChartNumber ( $most ) . ( $most == 1 ? ' point' : ' points' ) . ' a hexagon</text>';

    $px = fn ( $v ) => $margin + ( $v - $xLow ) / ( $xHigh - $xLow ) * $plotW;
    $py = fn ( $v ) => $top + ( $yHigh - $v ) / ( $yHigh - $yLow ) * $plotH;

    foreach ( $yTicks as $tick )
      $svg .= "<line class=\"pc-grid\" x1=\"$margin\" x2=\"" . ( $margin + $plotW ) . '" y1="' . padChartXY ( $py ( $tick ) ) . '" y2="' . padChartXY ( $py ( $tick ) ) . '"/>'
            . '<text x="' . ( $margin - 6 ) . '" y="' . padChartXY ( $py ( $tick ) + 4 ) . '" text-anchor="end">' . padChartNumber ( $tick ) . '</text>';

    foreach ( $xTicks as $tick )
      $svg .= '<line class="pc-grid" x1="' . padChartXY ( $px ( $tick ) ) . '" x2="' . padChartXY ( $px ( $tick ) ) . "\" y1=\"$top\" y2=\"" . ( $top + $plotH ) . '"/>'
            . '<text x="' . padChartXY ( $px ( $tick ) ) . '" y="' . ( $top + $plotH + 16 ) . '" text-anchor="middle">' . padChartNumber ( $tick ) . '</text>';

    $svg .= '<text x="' . padChartXY ( $margin + $plotW / 2 ) . '" y="' . ( $height - 6 ) . '" text-anchor="middle">' . padChartAttr ( $xField ) . '</text>'
          . '<text x="4" y="' . ( $top - 8 ) . '">' . padChartAttr ( $yField ) . '</text>';

    // The hexagons, row by row, cut at the plot: six corners around the middle, the first
    // at the top.

    $svg .= "<clipPath id=\"$id-plot\"><rect x=\"$margin\" y=\"$top\" width=\"$plotW\" height=\"$plotH\"/></clipPath><g clip-path=\"url(#$id-plot)\">";

    foreach ( $cells as $key => $count ) {

      list ( $cx, $cy ) = $middle ( $key );

      $d = '';
      for ( $k = 0; $k < 6; $k++ ) {
        $a  = M_PI / 3 * $k - M_PI / 2;
        $d .= ( $k ? 'L' : 'M' ) . padChartXY ( $margin + $cx + $r * cos ( $a ) ) . ',' . padChartXY ( $top + $cy + $r * sin ( $a ) );
      }

      $svg .= '<path class="pc-mark pc-seg pc-h' . intdiv ( $count * 6 + $most - 1, $most ) . "\" d=\"{$d}Z\"><title>" . padChartAttr ( $tip ( $key, $count ) ) . '</title></path>';

    }

    $svg .= '</g>' . "<line class=\"pc-axis\" x1=\"$margin\" x2=\"" . ( $margin + $plotW ) . '" y1="' . ( $top + $plotH ) . '" y2="' . ( $top + $plotH ) . '"/>';

    return "$svg</svg>";

  }

?>
