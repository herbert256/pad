<?php

  // Amounts over time as filled areas, and the streamgraph:
  //
  //   {chart 'area', data='energy', label='year', value='coal, gas, wind, solar', stacked}
  //   {chart 'stream', data='listening', label='month', value='pop, rock, jazz, hiphop'}
  //
  // area: value= lists the fields - else every numeric field but label= - each a light
  // area over the label= axis with its line on top, a legend above when there are several.
  // stacked lays them on top of each other, the top edge the running total; percent
  // stacks them to 100%, every column a whole. A stacked area takes no negative values.
  //
  // stream: the series stacked around a wandering middle instead of a baseline - the
  // wiggle offset, which keeps the slopes of the layers as small as it can, the layers
  // that peak early on the inside - in smooth curves, with no value axis: its job is how
  // the parts swell and shrink. The labels go below, the legend above.
  //
  // A column per label carries the values of all series in its tooltip.

  function padChartArea ( $kind, $rows, $width, $height ) {

    global $padCheckSyntax;

    $stream = ( $kind == 'stream' );

    list ( $numbers, $names ) = padChartGuess ( $rows );

    $used   = [];
    $label  = padChartPick ( 'label', $names, $used );
    $fields = padChartFields ( padTagParm ( 'value' ) ) ?: array_values ( array_diff ( $numbers, $used ) );

    if ( ! $fields )
      return '';

    $flag    = fn ( $name ) => ! in_array ( padTagParm ( $name, FALSE ), [ FALSE, '', 0, '0' ], TRUE );
    $percent = $stream ? FALSE : $flag ( 'percent' );
    $stacked = $stream ? TRUE  : ( $flag ( 'stacked' ) or $percent );

    if ( count ( $fields ) > 8 ) {
      if ( $padCheckSyntax )
        padError ( "the $kind chart has " . count ( $fields ) . ' series - eight at most, each its own colour' );
      $fields = array_slice ( $fields, 0, 8 );
    }

    list ( $labels, $series ) = padChartTable ( $rows, $label, $fields );

    $n = count ( $labels );

    if ( ! $n )
      return '';

    if ( $stacked )
      foreach ( $series as $name => $values )
        foreach ( $values as $i => $v )
          if ( $v < 0 ) {
            if ( $padCheckSyntax )
              padError ( "a stacked area takes no negative values - $name has " . padChartNumber ( $v ) . " at {$labels [$i]}" );
            $series [$name] [$i] = 0;
          }

    $names  = array_keys ( $series );
    $k      = count ( $names );
    $totals = array_fill ( 0, $n, 0 );

    foreach ( $series as $values )
      foreach ( $values as $i => $v )
        $totals [$i] += $v;

    // The edges of every layer: [ lower, upper ] per label.

    $lower = $upper = [];

    if ( ! $stacked )
      foreach ( $names as $name ) {
        $lower [$name] = array_fill ( 0, $n, 0 );
        $upper [$name] = $series [$name];
      }

    else {

      $order = $stream ? padChartAreaInsideOut ( $series ) : $names;
      $base  = $stream ? padChartAreaWiggle ( $series, $order, $n ) : array_fill ( 0, $n, 0 );

      $level = $base;

      foreach ( $order as $name ) {
        $lower [$name] = $level;
        foreach ( $series [$name] as $i => $v )
          $level [$i] += ( $percent ? ( $totals [$i] > 0 ? $v / $totals [$i] * 100 : 0 ) : $v );
        $upper [$name] = $level;
      }

    }

    $all = array_merge ( ...array_values ( $lower ), ...array_values ( $upper ) );

    if ( ! is_finite ( max ( $all ) - min ( $all ) ) )
      return '';

    $share = fn ( $i, $v ) => ( $totals [$i] > 0 ) ? ' (' . padChartNumber ( round ( $v / $totals [$i] * 100, 1 ) ) . '%)' : '';
    $desc  = [];

    foreach ( $labels as $i => $text ) {
      $parts = [];
      foreach ( $series as $name => $values )
        $parts [] = ( $k > 1 ? "$name " : '' ) . padChartNumber ( $values [$i] ) . ( $percent ? $share ( $i, $values [$i] ) : '' );
      $desc [] = "$text: " . implode ( ', ', $parts );
    }

    $title = (string) padTagParm ( 'title', implode ( ', ', $names ) . ( $label !== '' ? " by $label" : '' ) );

    list ( $id, $svg ) = padChartOpen ( $kind, [ $labels, $series, $stacked, $percent ], $desc, $title, $width, $height );

    // The value axis - none for a stream, which is drawn between its own lowest and highest
    // edge - and the margins around the plot.

    if ( $stream )
      $ticks = [];
    elseif ( $percent )
      $ticks = [ 0, 25, 50, 75, 100 ];
    else
      $ticks = padChartTicks ( min ( 0, min ( $all ) ), max ( 0, max ( $all ) ), $height < 200 ? 3 : 5 );

    $low  = $ticks ? $ticks [0]   : min ( $all );
    $high = $ticks ? end ( $ticks ) : max ( $all );

    if ( $high == $low )
      $high = $low + 1;

    if ( $stream ) {
      $room  = ( $high - $low ) * 0.04;
      $low  -= $room;
      $high += $room;
    }

    $tickW = 0;
    foreach ( $ticks as $tick )
      $tickW = max ( $tickW, padChartWidth ( padChartNumber ( $tick ) . ( $percent ? '%' : '' ) ) );

    $labelW = 0;
    foreach ( $labels as $text )
      $labelW = max ( $labelW, padChartWidth ( $text ) + 8 );

    $inset  = $stream ? min ( $labelW / 2, $width / 6 ) : 0;
    $left   = $stream ? 10 : max ( 24, (int) ceil ( $tickW ) + 10 );
    $right  = max ( 10, (int) ceil ( $labelW / 2 - $inset ) );
    $top    = 10;

    if ( $k > 1 ) {
      list ( $legend, $legendH ) = padChartLegend ( $names, $left, 6, $width - $left - $right );
      $svg .= $legend;
      $top += $legendH;
    }

    $bottom = 24;
    $plotW  = max ( 1, $width  - $left - $right );
    $plotH  = max ( 1, $height - $top  - $bottom );
    $step   = ( $n > 1 ) ? ( $plotW - 2 * $inset ) / ( $n - 1 ) : 0;
    $x      = fn ( $i ) => ( $n > 1 ) ? $left + $inset + $i * $step : $left + $plotW / 2;
    $y      = fn ( $v ) => $top + ( $high - $v ) / ( $high - $low ) * $plotH;

    foreach ( $ticks as $tick )
      $svg .= '<line class="pc-grid" x1="' . $left . '" x2="' . ( $width - $right ) . '" y1="' . padChartXY ( $y ( $tick ) ) . '" y2="' . padChartXY ( $y ( $tick ) ) . '"/>'
            . '<text x="' . ( $left - 6 ) . '" y="' . padChartXY ( $y ( $tick ) + 4 ) . '" text-anchor="end">' . padChartNumber ( $tick ) . ( $percent ? '%' : '' ) . '</text>';

    // The labels under the plot, thinned out when they would overlap.

    $every = ( $n > 1 ) ? max ( 1, (int) ceil ( $labelW / max ( 1, $step ) ) ) : 1;

    foreach ( $labels as $i => $text )
      if ( $i % $every == 0 )
        $svg .= '<text x="' . padChartXY ( $x ( $i ) ) . '" y="' . ( $height - 8 ) . '" text-anchor="middle">' . padChartAttr ( $text ) . '</text>';

    // The layers: a path along the upper edge and back along the lower one - smooth in a
    // stream, straight lines in an area. One label alone is a column one step wide.

    $edge = function ( $values, $back ) use ( $x, $y, $n, $stream ) {
      $xs = $ys = [];
      foreach ( $values as $i => $v ) {
        $xs [] = $x ( $i );
        $ys [] = $y ( $v );
      }
      return padChartAreaEdge ( $xs, $ys, $stream, $back );
    };

    foreach ( $names as $j => $name ) {

      $class = padChartSlot ( $j );
      $sum   = padChartNumber ( array_sum ( $series [$name] ) );

      if ( $n == 1 ) {
        $x0 = $x ( 0 ) - $plotW / 4;
        $d  = 'M' . padChartXY ( $x0 ) . ',' . padChartXY ( $y ( $upper [$name] [0] ) ) . 'H' . padChartXY ( $x0 + $plotW / 2 )
            . 'V' . padChartXY ( $y ( $lower [$name] [0] ) ) . 'H' . padChartXY ( $x0 ) . 'Z';
      } else
        $d = $edge ( $upper [$name], FALSE ) . 'L' . substr ( $edge ( $lower [$name], TRUE ), 1 ) . 'Z';

      if ( $stacked )
        $svg .= "<path class=\"$class pc-seg\" fill-opacity=\"" . ( $stream ? '0.9' : '0.85' ) . "\" d=\"$d\"><title>" . padChartAttr ( "$name: $sum in all" ) . '</title></path>';
      else
        $svg .= "<path class=\"$class\" fill-opacity=\"" . ( $k > 1 ? '0.18' : '0.25' ) . "\" d=\"$d\"><title>" . padChartAttr ( "$name: $sum in all" ) . '</title></path>'
              . '<path class="pc-l' . ( $j + 1 ) . '" d="' . ( $n > 1 ? $edge ( $upper [$name], FALSE ) : '' ) . '"/>';

    }

    if ( ! $stream )
      $svg .= '<line class="pc-axis" x1="' . $left . '" x2="' . ( $width - $right ) . '" y1="' . padChartXY ( $y ( max ( $low, 0 ) ) ) . '" y2="' . padChartXY ( $y ( max ( $low, 0 ) ) ) . '"/>';

    // A column per label on top, its tooltip the values of every series there.

    $colW = ( $n > 1 ) ? $step : $plotW;

    foreach ( $labels as $i => $text )
      $svg .= '<rect class="pc-hit" x="' . padChartXY ( max ( $left, $x ( $i ) - $colW / 2 ) ) . "\" y=\"$top\""
            . ' width="' . padChartXY ( min ( $x ( $i ) + $colW / 2, $width - $right ) - max ( $left, $x ( $i ) - $colW / 2 ) ) . "\" height=\"$plotH\">"
            . '<title>' . padChartAttr ( $desc [$i] ) . '</title></rect>';

    return "$svg</svg>";

  }

  // The order of a stream's layers, from the inside out: sorted by where each peaks, every
  // next one laid on the lighter of the two sides - the early peaks in the middle, the
  // late ones at the edges, as d3's insideOut.

  function padChartAreaInsideOut ( $series ) {

    $peaks = [];

    foreach ( $series as $name => $values )
      $peaks [$name] = array_search ( max ( $values ), $values );

    $names = array_keys ( $series );

    usort ( $names, fn ( $a, $b ) => $peaks [$a] <=> $peaks [$b] ?: array_search ( $a, array_keys ( $series ) ) <=> array_search ( $b, array_keys ( $series ) ) );

    $top = $bottom = [];
    $up  = $down   = 0;

    foreach ( $names as $name )
      if ( $up < $down ) {
        $top [] = $name;
        $up    += array_sum ( $series [$name] );
      } else {
        $bottom [] = $name;
        $down     += array_sum ( $series [$name] );
      }

    return array_merge ( array_reverse ( $bottom ), $top );

  }

  // The baseline of a stream: the wiggle offset of Byron and Wattenberg, which moves the
  // whole stack at each step so that the weighted slope of its layers is as small as it
  // can be - then the stack is centred around zero over the whole width.

  function padChartAreaWiggle ( $series, $order, $n ) {

    $base = [ 0 ];
    $y    = 0;

    for ( $j = 1; $j < $n; $j++ ) {

      $s1 = $s2 = 0;
      $below = 0;

      foreach ( $order as $name ) {
        $now    = $series [$name] [$j];
        $before = $series [$name] [$j - 1];
        $s3     = ( $now - $before ) / 2 + $below;
        $below += $now - $before;
        $s1    += $now;
        $s2    += $s3 * $now;
      }

      if ( $s1 )
        $y -= $s2 / $s1;

      $base [] = $y;

    }

    $mid = [];

    for ( $j = 0; $j < $n; $j++ ) {
      $sum = 0;
      foreach ( $order as $name )
        $sum += $series [$name] [$j];
      $mid [] = $base [$j] + $sum / 2;
    }

    $shift = ( max ( $mid ) + min ( $mid ) ) / 2;

    return array_map ( fn ( $b ) => $b - $shift, $base );

  }

  // An edge of a layer as path text from its first point to its last - from the last to the
  // first when $back. Smooth: a cubic curve through every point with the tangents of a
  // monotone spline, so it never swings past a point; the same control points either way,
  // so the edge two layers share is the same curve in both.

  function padChartAreaEdge ( $xs, $ys, $smooth, $back ) {

    $n = count ( $xs );

    if ( $back ) {
      $at = $n - 1;
      $d  = 'M' . padChartXY ( $xs [$at] ) . ',' . padChartXY ( $ys [$at] );
    } else
      $d = 'M' . padChartXY ( $xs [0] ) . ',' . padChartXY ( $ys [0] );

    if ( ! $smooth or $n < 3 ) {
      $range = $back ? range ( $n - 2, 0 ) : range ( 1, $n - 1 );
      foreach ( $n > 1 ? $range : [] as $i )
        $d .= 'L' . padChartXY ( $xs [$i] ) . ',' . padChartXY ( $ys [$i] );
      return $d;
    }

    $t = padChartAreaTangents ( $xs, $ys );

    $segment = function ( $a, $b ) use ( $xs, $ys, $t ) {
      $h = ( $xs [$b] - $xs [$a] ) / 3;
      return 'C' . padChartXY ( $xs [$a] + $h ) . ',' . padChartXY ( $ys [$a] + $t [$a] * $h )
           . ' ' . padChartXY ( $xs [$b] - $h ) . ',' . padChartXY ( $ys [$b] - $t [$b] * $h )
           . ' ' . padChartXY ( $xs [$b] ) . ',' . padChartXY ( $ys [$b] );
    };

    if ( $back )
      for ( $i = $n - 1; $i > 0; $i-- )
        $d .= $segment ( $i, $i - 1 );
    else
      for ( $i = 0; $i < $n - 1; $i++ )
        $d .= $segment ( $i, $i + 1 );

    return $d;

  }

  // The tangent at every point of a monotone cubic spline (Steffen's method, as d3's
  // monotoneX): flat at a peak or a dip, never steeper than the slopes beside it allow.

  function padChartAreaTangents ( $xs, $ys ) {

    $n = count ( $xs );
    $s = $t = [];

    for ( $i = 0; $i < $n - 1; $i++ )
      $s [$i] = ( $xs [$i + 1] != $xs [$i] ) ? ( $ys [$i + 1] - $ys [$i] ) / ( $xs [$i + 1] - $xs [$i] ) : 0;

    for ( $i = 1; $i < $n - 1; $i++ ) {
      $h0 = $xs [$i] - $xs [$i - 1];
      $h1 = $xs [$i + 1] - $xs [$i];
      $p  = ( $h0 + $h1 ) ? ( $s [$i - 1] * $h1 + $s [$i] * $h0 ) / ( $h0 + $h1 ) : 0;
      $t [$i] = ( ( $s [$i - 1] <=> 0 ) + ( $s [$i] <=> 0 ) ) * min ( abs ( $s [$i - 1] ), abs ( $s [$i] ), 0.5 * abs ( $p ) );
    }

    $t [0]      = ( 3 * $s [0] - $t [1] ) / 2;
    $t [$n - 1] = ( 3 * $s [$n - 2] - $t [$n - 2] ) / 2;

    ksort ( $t );

    return $t;

  }

?>
