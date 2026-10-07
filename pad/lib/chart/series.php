<?php

  // Bar and line charts of several series, and horizontal bars:
  //
  //   {chart 'bar',  data='sales', label='month', value='online, shop'}           grouped
  //   {chart 'bar',  data='sales', label='month', value='online, shop', stacked}  stacked
  //   {chart 'hbar', data='teams', label='team', value='score'}                   sideways
  //   {chart 'line', data='visits', label='day', value='mobile, desktop'}         a line each
  //
  // Several series get a legend above the plot, in the fixed order of the slots; up to
  // four lines are also named at their last point. One series in a bar or line chart is
  // drawn by padChart in lib/chart.php.

  function padChartSeries ( $kind, $labels, $series, $title, $width, $height, $stacked ) {

    global $padCheckSyntax;

    $n = count ( $labels );

    if ( ! $n )
      return '';

    if ( count ( $series ) > 8 ) {
      if ( $padCheckSyntax )
        padError ( 'the chart has ' . count ( $series ) . ' series - eight at most, each its own colour' );
      $series = array_slice ( $series, 0, 8, TRUE );
    }

    $names      = array_keys ( $series );
    $k          = count ( $names );
    $horizontal = ( $kind == 'hbar' );
    $line       = ( $kind == 'line' );
    $desc       = [];

    foreach ( $labels as $i => $label ) {
      $parts = [];
      foreach ( $series as $name => $values )
        $parts [] = ( $k > 1 ? "$name " : '' ) . padChartNumber ( $values [$i] );
      $desc [] = $label . ': ' . implode ( ', ', $parts );
    }

    list ( $id, $svg ) = padChartOpen ( $kind, [ $labels, $series, $stacked ], $desc, $title, $width, $height );

    // The value domain: every value, or the sums of a category's positive and of its
    // negative parts when stacked - and zero, the base of the bars.

    $min = $max = 0;

    for ( $i = 0; $i < $n; $i++ ) {
      $up = $down = 0;
      foreach ( $series as $values )
        if ( $stacked ) {
          if ( $values [$i] >= 0 ) $up   += $values [$i];
          else                     $down += $values [$i];
        } else {
          $up   = max ( $up,   $values [$i] );
          $down = min ( $down, $values [$i] );
        }
      $max = max ( $max, $up );
      $min = min ( $min, $down );
    }

    if ( ! is_finite ( $max - $min ) )
      return '';

    $ticks = padChartTicks ( $min, $max, ( $horizontal ? $width : $height ) < 200 ? 3 : 5 );
    $low   = $ticks [0];
    $high  = end ( $ticks );

    $tickWidth = 0;
    foreach ( $ticks as $tick )
      $tickWidth = max ( $tickWidth, padChartWidth ( padChartNumber ( $tick ) ) );

    $labelWidth = 0;
    foreach ( $labels as $label )
      $labelWidth = max ( $labelWidth, padChartWidth ( $label ) + 8 );

    // The margins: the tick labels on the left of a vertical chart, the category labels on
    // the left of a horizontal one; the names of the lines on the right of the plot.

    $right = 10;

    if ( $line and $k > 1 and $k <= 4 ) {
      $nameWidth = 0;
      foreach ( $names as $name )
        $nameWidth = max ( $nameWidth, padChartWidth ( $name ) );
      $right = (int) min ( $width * 0.3, $nameWidth + 18 );
    }

    if ( $horizontal ) {
      $left  = (int) max ( 24, min ( $width * 0.4, $labelWidth + 4 ) );
      $right = (int) max ( $right, ceil ( padChartWidth ( padChartNumber ( $high ) ) / 2 ) + 4 );
    } else
      $left = max ( 24, (int) ceil ( $tickWidth ) + 10 );

    $top = 10;

    if ( $k > 1 ) {
      list ( $legend, $legendHeight ) = padChartLegend ( $names, $left, 6, $width - $left - $right );
      $svg .= $legend;
      $top += $legendHeight;
    }

    $bottom = 24;
    $plotW  = max ( 1, $width  - $left - $right );
    $plotH  = max ( 1, $height - $top  - $bottom );
    $band   = ( $horizontal ? $plotH : $plotW ) / $n;

    // $at places a value along the value axis, $mid the middle of a category's band.

    if ( $horizontal ) {
      $at  = fn ( $v ) => $left + ( $v - $low ) / ( $high - $low ) * $plotW;
      $mid = fn ( $i ) => $top + ( $i + 0.5 ) * $band;
    } else {
      $at  = fn ( $v ) => $top + ( $high - $v ) / ( $high - $low ) * $plotH;
      $mid = fn ( $i ) => $left + ( $i + 0.5 ) * $band;
    }

    foreach ( $ticks as $tick ) {
      $p = padChartXY ( $at ( $tick ) );
      if ( $horizontal )
        $svg .= "<line class=\"pc-grid\" x1=\"$p\" x2=\"$p\" y1=\"$top\" y2=\"" . ( $top + $plotH ) . '"/>'
              . "<text x=\"$p\" y=\"" . ( $height - 8 ) . '" text-anchor="middle">' . padChartNumber ( $tick ) . '</text>';
      else
        $svg .= "<line class=\"pc-grid\" x1=\"$left\" x2=\"" . ( $width - $right ) . "\" y1=\"$p\" y2=\"$p\"/>"
              . '<text x="' . ( $left - 6 ) . '" y="' . padChartXY ( $at ( $tick ) + 4 ) . '" text-anchor="end">' . padChartNumber ( $tick ) . '</text>';
    }

    // The category labels, thinned out when they would overlap.

    $every = $horizontal ? max ( 1, (int) ceil ( 13 / max ( 1, $band ) ) )
                         : max ( 1, (int) ceil ( $labelWidth / max ( 1, $band ) ) );

    foreach ( $labels as $i => $label )
      if ( $i % $every == 0 ) {
        if ( $horizontal )
          $svg .= '<text x="' . ( $left - 6 ) . '" y="' . padChartXY ( $mid ( $i ) + 4 ) . '" text-anchor="end">' . padChartAttr ( padChartFit ( $label, $left - 10 ) ) . '</text>';
        else
          $svg .= '<text x="' . padChartXY ( $mid ( $i ) ) . '" y="' . ( $height - 8 ) . '" text-anchor="middle">' . padChartAttr ( $label ) . '</text>';
      }

    $base = $at ( 0 );
    $tip  = fn ( $i, $name ) => padChartAttr ( $labels [$i] . ( $k > 1 ? " · $name" : '' ) . ': ' . padChartNumber ( $series [$name] [$i] ) );

    if ( $line )
      $svg .= padChartSeriesLines ( $series, $labels, $at, $mid, $band, $tip, $top, $height - $bottom, $width );

    elseif ( $stacked ) {

      // Stacked: one bar per category, its parts square and parted by a 1px surface line.

      $barW = max ( 1, min ( 24, $band * 0.7 ) );

      for ( $i = 0; $i < $n; $i++ ) {

        $up = $down = 0;

        foreach ( $names as $j => $name ) {

          $v = $series [$name] [$i];

          if ( $v >= 0 ) { $from = $up;   $up   += $v; $to = $up;   }
          else           { $from = $down; $down += $v; $to = $down; }

          $a = $at ( $from );
          $b = $at ( $to );
          $c = $mid ( $i ) - $barW / 2;

          if ( $horizontal )
            $box = 'x="' . padChartXY ( min ( $a, $b ) ) . '" y="' . padChartXY ( $c ) . '" width="' . padChartXY ( abs ( $b - $a ) ) . '" height="' . padChartXY ( $barW ) . '"';
          else
            $box = 'x="' . padChartXY ( $c ) . '" y="' . padChartXY ( min ( $a, $b ) ) . '" width="' . padChartXY ( $barW ) . '" height="' . padChartXY ( abs ( $b - $a ) ) . '"';

          $svg .= '<rect class="pc-mark pc-seg ' . padChartSlot ( $j ) . "\" $box><title>" . $tip ( $i, $name ) . '</title></rect>';

        }

      }

    } else {

      // Grouped: the bars of a category side by side, 2px apart, no thicker than 24.

      $slot = ( $k == 1 ) ? max ( 1, min ( 24, $band * 0.7 ) ) : min ( $band * 0.8 / $k, 26 );
      $barW = ( $k == 1 ) ? $slot : max ( 1, $slot - 2 );

      for ( $i = 0; $i < $n; $i++ )
        foreach ( $names as $j => $name ) {
          $c = $mid ( $i ) + ( $j - ( $k - 1 ) / 2 ) * $slot;
          $d = padChartBarPath ( $c - $barW / 2, $c + $barW / 2, $base, $at ( $series [$name] [$i] ), ! $horizontal );
          $svg .= '<path class="pc-mark ' . padChartSlot ( $j ) . "\" d=\"$d\"><title>" . $tip ( $i, $name ) . '</title></path>';
        }

    }

    $b = padChartXY ( $base );

    if ( $horizontal )
      $svg .= "<line class=\"pc-axis\" x1=\"$b\" x2=\"$b\" y1=\"$top\" y2=\"" . ( $top + $plotH ) . '"/>';
    else
      $svg .= "<line class=\"pc-axis\" x1=\"$left\" x2=\"" . ( $width - $right ) . "\" y1=\"$b\" y2=\"$b\"/>";

    return "$svg</svg>";

  }

  // A 2px line per series, its last point marked and - up to four lines - named there, the
  // names moved apart where they would overlap. Every point has a larger invisible target
  // that carries its tooltip.

  function padChartSeriesLines ( $series, $labels, $at, $mid, $band, $tip, $top, $bottom, $edge ) {

    $svg  = $hits = '';
    $n    = count ( $labels );
    $hit  = padChartXY ( max ( 8, min ( 20, $band / 2 ) ) );
    $ends = [];

    foreach ( array_keys ( $series ) as $j => $name ) {

      $line = '';
      foreach ( $series [$name] as $i => $v ) {
        $line .= ( $i ? 'L' : 'M' ) . padChartXY ( $mid ( $i ) ) . ',' . padChartXY ( $at ( $v ) );
        $hits .= '<circle class="pc-hit" cx="' . padChartXY ( $mid ( $i ) ) . '" cy="' . padChartXY ( $at ( $v ) ) . "\" r=\"$hit\"><title>" . $tip ( $i, $name ) . '</title></circle>';
      }

      $last = $at ( $series [$name] [$n - 1] );

      $svg .= '<path class="pc-l' . ( $j + 1 ) . "\" d=\"$line\"/>"
            . '<circle class="pc-c' . ( $j + 1 ) . ' pc-ring" cx="' . padChartXY ( $mid ( $n - 1 ) ) . '" cy="' . padChartXY ( $last ) . '" r="4"/>';

      $ends [$name] = $last;

    }

    if ( count ( $series ) > 1 and count ( $series ) <= 4 ) {

      asort ( $ends );

      $y = [];
      $previous = -INF;
      foreach ( $ends as $name => $end )
        $previous = $y [$name] = max ( $end, $previous + 12 );

      $over = end ( $y ) - ( $bottom - 2 );
      if ( $over > 0 )
        foreach ( $y as $name => $value )
          $y [$name] = max ( $top + 4, $value - $over );

      foreach ( $y as $name => $value )
        $svg .= '<text x="' . padChartXY ( $mid ( $n - 1 ) + 8 ) . '" y="' . padChartXY ( $value + 4 ) . '">'
              . padChartAttr ( padChartFit ( $name, $edge - $mid ( $n - 1 ) - 12 ) ) . '</text>';

    }

    return $svg . $hits;

  }

?>
