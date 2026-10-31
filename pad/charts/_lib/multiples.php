<?php

  // Small multiples: the same small line chart once per group, on one scale, in a grid -
  // so the eye compares the shapes.
  //
  //   {chart 'multiples', data='climate', by='city', label='month', value='temperature'}
  //   {chart 'multiples', data='climate', by='city', columns=3}
  //
  // by= names the field that groups the rows - else the first field that is no number;
  // label= the field along the x axis - else the next one; value= the number - else the
  // first numeric field. Every panel has the same x positions (the labels in the order
  // they first come) and the same y scale, which takes in zero; a line over a light wash,
  // its last point marked. columns= sets the number of panels side by side - else as many
  // as give the panels the best shape. The value ticks stand left of the first column,
  // the first and last label under the bottom row.

  function padChartMultiples ( $rows, $width, $height ) {

    global $padCheckSyntax;

    list ( $numbers, $names ) = padChartGuess ( $rows );

    $used  = [];
    $by    = padChartPick ( 'by',    $names,   $used );
    $label = padChartPick ( 'label', $names,   $used );
    $value = padChartPick ( 'value', $numbers, $used );

    if ( $by === '' or $value === '' ) {
      if ( $padCheckSyntax and $rows )
        padError ( "small multiples need a field to group by and a number - by='field' and value='field'" );
      return '';
    }

    $groups = $labels = [];
    $index  = 0;

    foreach ( (array) $rows as $row ) {

      $row = padChartRow ( $row );
      $index++;
      $v   = $row [$value] ?? NULL;

      if ( ! padChartFinite ( $v ) )
        continue;

      $group = padChartText ( $row, $by );
      $text  = padChartText ( $row, $label );
      $text  = ( $text !== '' ) ? $text : (string) $index;

      if ( ! isset ( $labels [$text] ) )
        $labels [$text] = count ( $labels );

      $groups [$group] [$labels [$text]] = $v + 0;

    }

    if ( ! $groups )
      return '';

    foreach ( $groups as $group => $points )
      ksort ( $groups [$group] );

    $labels = array_keys ( $labels );
    $n      = count ( $labels );
    $g      = count ( $groups );
    $all    = array_merge ( ...array_values ( array_map ( 'array_values', $groups ) ) );
    $desc   = [];

    foreach ( $groups as $group => $points ) {
      $parts = [];
      foreach ( $points as $i => $v )
        $parts [] = $labels [$i] . ' ' . padChartNumber ( $v );
      $desc [] = "$group: " . implode ( ', ', $parts );
    }

    $title = (string) padTagParm ( 'title', ucfirst ( $value ) . ( $label !== '' ? " by $label" : '' ) . ", per $by" );

    list ( $id, $svg ) = padChartOpen ( 'multiples', [ $labels, $groups ], $desc, $title, $width, $height );

    // The common scale, four ticks or so, and the margin its labels take.

    $ticks = padChartTicks ( min ( 0, min ( $all ) ), max ( 0, max ( $all ) ), 4 );
    $low   = $ticks [0];
    $high  = end ( $ticks );

    $tickW = 0;
    foreach ( $ticks as $tick )
      $tickW = max ( $tickW, padChartWidth ( padChartNumber ( $tick ) ) );

    $left   = max ( 20, (int) ceil ( $tickW ) + 8 );
    $right  = 8;
    $top    = 4;
    $bottom = 18;

    // The grid: columns= when given, else the count whose panels come nearest to 1.6 : 1.

    $columns = (int) padTagParm ( 'columns', 0 );

    if ( $columns < 1 ) {
      $best = -1;
      for ( $c = 1; $c <= $g; $c++ ) {
        $cellW = ( $width  - $left - $right ) / $c;
        $cellH = ( $height - $top - $bottom ) / ceil ( $g / $c );
        $score = min ( $cellW / 1.6, $cellH );
        if ( $score > $best ) {
          $best    = $score;
          $columns = $c;
        }
      }
    }

    $columns = min ( $columns, $g );
    $lines   = (int) ceil ( $g / $columns );
    $cellW   = ( $width  - $left - $right ) / $columns;
    $cellH   = ( $height - $top - $bottom ) / $lines;
    $gap     = 12;
    $head    = 18;

    // One panel: its name above, the grid lines of the common ticks, the wash and the line,
    // and a target per point that carries the tooltip.

    $j = 0;

    foreach ( $groups as $group => $points ) {

      $col   = $j % $columns;
      $line  = intdiv ( $j, $columns );
      $x0    = $left + $col * $cellW;
      $y0    = $top + $line * $cellH + $head;
      $plotW = max ( 1, $cellW - $gap );
      $plotH = max ( 1, $cellH - $head - 6 );
      $x     = fn ( $i ) => $n > 1 ? $x0 + $i / ( $n - 1 ) * $plotW : $x0 + $plotW / 2;
      $y     = fn ( $v ) => $y0 + ( $high - $v ) / ( $high - $low ) * $plotH;

      $svg .= '<text x="' . padChartXY ( $x0 ) . '" y="' . padChartXY ( $y0 - 6 ) . '" style="font-weight:600">' . padChartAttr ( padChartFit ( $group, $plotW ) ) . '</text>';

      foreach ( $ticks as $tick ) {
        $svg .= '<line class="pc-grid" x1="' . padChartXY ( $x0 ) . '" x2="' . padChartXY ( $x0 + $plotW ) . '" y1="' . padChartXY ( $y ( $tick ) ) . '" y2="' . padChartXY ( $y ( $tick ) ) . '"/>';
        if ( $col == 0 )
          $svg .= '<text x="' . padChartXY ( $x0 - 5 ) . '" y="' . padChartXY ( $y ( $tick ) + 4 ) . '" text-anchor="end">' . padChartNumber ( $tick ) . '</text>';
      }

      if ( $line == $lines - 1 or $j + $columns >= $g ) {
        $svg .= '<text x="' . padChartXY ( $x ( 0 ) ) . '" y="' . padChartXY ( $y0 + $plotH + 14 ) . '"' . ( $n > 1 ? '' : ' text-anchor="middle"' ) . '>'
              . padChartAttr ( padChartFit ( $labels [0], $plotW / 2 ) ) . '</text>';
        if ( $n > 1 )
          $svg .= '<text x="' . padChartXY ( $x ( $n - 1 ) ) . '" y="' . padChartXY ( $y0 + $plotH + 14 ) . '" text-anchor="end">'
                . padChartAttr ( padChartFit ( $labels [$n - 1], $plotW / 2 ) ) . '</text>';
      }

      $base = $y ( max ( $low, min ( 0, $high ) ) );
      $path = '';
      $hits = '';
      $hit  = padChartXY ( max ( 4, min ( 12, $n > 1 ? $plotW / ( $n - 1 ) / 2 : 12 ) ) );

      foreach ( $points as $i => $v ) {
        $path .= ( $path === '' ? 'M' : 'L' ) . padChartXY ( $x ( $i ) ) . ',' . padChartXY ( $y ( $v ) );
        $hits .= '<circle class="pc-hit" cx="' . padChartXY ( $x ( $i ) ) . '" cy="' . padChartXY ( $y ( $v ) ) . "\" r=\"$hit\"><title>"
               . padChartAttr ( "$group · {$labels [$i]}: " . padChartNumber ( $v ) ) . '</title></circle>';
      }

      $first = array_key_first ( $points );
      $last  = array_key_last  ( $points );

      $svg .= '<path class="pc-area" d="' . $path . 'L' . padChartXY ( $x ( $last ) ) . ',' . padChartXY ( $base ) . 'L' . padChartXY ( $x ( $first ) ) . ',' . padChartXY ( $base ) . 'Z"/>'
            . '<line class="pc-axis" x1="' . padChartXY ( $x0 ) . '" x2="' . padChartXY ( $x0 + $plotW ) . '" y1="' . padChartXY ( $base ) . '" y2="' . padChartXY ( $base ) . '" stroke-opacity="0.5"/>'
            . '<path class="pc-line" d="' . $path . '"/>'
            . '<circle class="pc-dot" cx="' . padChartXY ( $x ( $last ) ) . '" cy="' . padChartXY ( $y ( $points [$last] ) ) . '" r="3"/>'
            . $hits;

      $j++;

    }

    return "$svg</svg>";

  }

?>
