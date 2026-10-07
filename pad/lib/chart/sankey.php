<?php

  // Flows: {chart 'sankey', data='energy', source='from', target='to', value='amount'}.
  //
  // A row is a flow from one node to another; source= and target= name the fields - else
  // the first two that are no number - and value= the amount, else the first numeric
  // field. A flow of nothing, a negative one and one from a node to itself are left out;
  // flows between the same two nodes add up. A node stands in the column of the longest
  // path that reaches it, a node that sends nothing in the last column, and its height is
  // the larger of what comes in and what goes out. Each band is as wide as its flow.
  //
  // The flows cannot go round in a circle: one that does is reported under the strict
  // check, and the chart draws nothing.

  function padChartSankey ( $rows, $width, $height ) {

    global $padCheckSyntax;

    list ( $numbers, $names ) = padChartGuess ( $rows );

    $used   = [];
    $sField = padChartPick ( 'source', $names,   $used );
    $tField = padChartPick ( 'target', $names,   $used );
    $vField = padChartPick ( 'value',  $numbers, $used );

    if ( ( $sField === '' or $tField === '' ) and $padCheckSyntax )
      padError ( "the sankey has no field for the two ends of a flow - source='name', target='name'" );

    $title = (string) padTagParm ( 'title', 'Flows from ' . ( $sField ?: 'source' ) . ' to ' . ( $tField ?: 'target' ) );

    $links = [];

    foreach ( (array) $rows as $row ) {

      $row = padChartRow ( $row );
      $v   = $row [$vField] ?? NULL;
      $s   = padChartText ( $row, $sField );
      $t   = padChartText ( $row, $tField );

      if ( ! padChartFinite ( $v ) or $v <= 0 or $s === $t )
        continue;

      $key = serialize ( [ $s, $t ] );

      if ( isset ( $links [$key] ) )
        $links [$key] [2] += $v;
      else
        $links [$key] = [ $s, $t, $v + 0 ];

    }

    $links = array_values ( $links );

    if ( ! $links )
      return '';

    // The nodes in the order they first appear, and their columns: the longest path from a
    // node nothing flows into, taken in topological order.

    $nodes = $in = $out = $next = $degree = [];

    foreach ( $links as $l => list ( $s, $t, $v ) ) {
      foreach ( [ $s, $t ] as $node )
        if ( ! isset ( $nodes [$node] ) ) {
          $nodes  [$node] = count ( $nodes );
          $in     [$node] = $out [$node] = 0;
          $next   [$node] = [];
          $degree [$node] = 0;
        }
      $out  [$s] += $v;
      $in   [$t] += $v;
      $next [$s] [] = $t;
      $degree [$t]++;
    }

    $column = array_fill_keys ( array_keys ( $nodes ), 0 );
    $queue  = [];

    foreach ( $degree as $node => $count )
      if ( ! $count )
        $queue [] = $node;

    $done = 0;

    while ( $queue ) {
      $node = array_shift ( $queue );
      $done++;
      foreach ( $next [$node] as $to ) {
        $column [$to] = max ( $column [$to], $column [$node] + 1 );
        if ( ! --$degree [$to] )
          $queue [] = $to;
      }
    }

    if ( $done < count ( $nodes ) ) {
      if ( $padCheckSyntax ) {
        $circle = array_keys ( array_filter ( $degree ) );
        padError ( "the flows of the sankey go round in a circle through '" . padMakeSafe ( (string) $circle [0], 40 ) . "'" );
      }
      return '';
    }

    $last = max ( $column );

    foreach ( $nodes as $node => $i )
      if ( ! $out [$node] )
        $column [$node] = $last;

    $desc = [];
    foreach ( $links as list ( $s, $t, $v ) )
      $desc [] = "$s → $t: " . padChartNumber ( $v );

    list ( $id, $svg ) = padChartOpen ( 'sankey', $links, $desc, $title, $width, $height );

    // The layout: the columns spread over the width, the nodes of a column stacked with a
    // gap and centred, all at one scale - the one the fullest column fits at.

    $size = [];
    foreach ( $nodes as $node => $i )
      $size [$node] = max ( $in [$node], $out [$node] );

    $columns = array_fill ( 0, $last + 1, [] );
    foreach ( $nodes as $node => $i )
      $columns [$column [$node]] [] = $node;

    $top   = 10;
    $plotH = max ( 1, $height - 20 );
    $nodeW = 12;
    $most  = max ( array_map ( 'count', $columns ) );
    $gap   = min ( 10, $plotH * 0.4 / max ( 1, $most - 1 ) );
    $scale = INF;

    foreach ( $columns as $list )
      if ( $list )
        $scale = min ( $scale, ( $plotH - ( count ( $list ) - 1 ) * $gap ) / array_sum ( array_map ( fn ( $node ) => $size [$node], $list ) ) );

    if ( ! is_finite ( $scale ) or $scale <= 0 )
      return '';

    $x = $y = $h = [];

    foreach ( $columns as $c => $list ) {

      // After the first column, the nodes are ordered by the height of what flows into
      // them - the middle of their sources, weighed by the flow - so fewer bands cross.

      if ( $c > 0 ) {
        // A node's name as an array key is an integer when it reads as one, so the names
        // are compared loosely.

        $centre = [];
        foreach ( $list as $node ) {
          $sum = $weight = 0;
          foreach ( $links as list ( $s, $t, $v ) )
            if ( $t == $node and isset ( $y [$s] ) ) {
              $sum    += ( $y [$s] + $h [$s] / 2 ) * $v;
              $weight += $v;
            }
          $centre [$node] = $weight ? $sum / $weight : INF;
        }
        usort ( $list, fn ( $a, $b ) => [ $centre [$a], $nodes [$a] ] <=> [ $centre [$b], $nodes [$b] ] );
      }

      $total = array_sum ( array_map ( fn ( $node ) => $size [$node], $list ) ) * $scale + ( count ( $list ) - 1 ) * $gap;
      $at    = $top + ( $plotH - $total ) / 2;

      foreach ( $list as $node ) {
        $x [$node] = 10 + $c * ( $width - 20 - $nodeW ) / max ( 1, $last );
        $y [$node] = $at;
        $h [$node] = max ( 1, $size [$node] * $scale );
        $at       += $size [$node] * $scale + $gap;
      }

    }

    // The bands, leaving a node in the order of their targets and entering one in the order
    // of their sources.

    $order = array_keys ( $links );
    usort ( $order, fn ( $a, $b ) => [ $y [$links [$a] [1]], $a ] <=> [ $y [$links [$b] [1]], $b ] );
    $fromY = $leave = $enter = [];

    foreach ( $order as $l ) {
      $s = $links [$l] [0];
      $fromY [$l] = $y [$s] + ( $leave [$s] ?? 0 );
      $leave [$s] = ( $leave [$s] ?? 0 ) + $links [$l] [2] * $scale;
    }

    usort ( $order, fn ( $a, $b ) => [ $y [$links [$a] [0]], $a ] <=> [ $y [$links [$b] [0]], $b ] );

    foreach ( $order as $l ) {

      list ( $s, $t, $v ) = $links [$l];

      $band = $v * $scale;
      $y0   = $fromY [$l];
      $y1   = $y [$t] + ( $enter [$t] ?? 0 );
      $x0   = $x [$s] + $nodeW;
      $x1   = $x [$t];
      $xm   = ( $x0 + $x1 ) / 2;

      $enter [$t] = ( $enter [$t] ?? 0 ) + $band;

      $d = 'M' . padChartXY ( $x0 ) . ',' . padChartXY ( $y0 )
         . 'C' . padChartXY ( $xm ) . ',' . padChartXY ( $y0 ) . ' ' . padChartXY ( $xm ) . ',' . padChartXY ( $y1 ) . ' ' . padChartXY ( $x1 ) . ',' . padChartXY ( $y1 )
         . 'V' . padChartXY ( $y1 + $band )
         . 'C' . padChartXY ( $xm ) . ',' . padChartXY ( $y1 + $band ) . ' ' . padChartXY ( $xm ) . ',' . padChartXY ( $y0 + $band ) . ' ' . padChartXY ( $x0 ) . ',' . padChartXY ( $y0 + $band )
         . 'Z';

      $svg .= "<path class=\"pc-link\" d=\"$d\"><title>" . padChartAttr ( "$s → $t: " . padChartNumber ( $v ) ) . '</title></path>';

    }

    // The nodes, named beside them: on the right, and on the left in the last column.

    foreach ( $nodes as $node => $i ) {

      $svg .= '<rect class="pc-mark pc-c1" x="' . padChartXY ( $x [$node] ) . '" y="' . padChartXY ( $y [$node] ) . "\" width=\"$nodeW\" height=\"" . padChartXY ( $h [$node] ) . '"><title>'
            . padChartAttr ( $node . ': ' . padChartNumber ( $size [$node] ) ) . '</title></rect>';

      $end = ( $column [$node] == $last );

      $svg .= '<text x="' . padChartXY ( $end ? $x [$node] - 6 : $x [$node] + $nodeW + 6 ) . '" y="' . padChartXY ( $y [$node] + $h [$node] / 2 + 4 ) . '"'
            . ( $end ? ' text-anchor="end"' : '' ) . '>' . padChartAttr ( $node ) . '</text>';

    }

    return "$svg</svg>";

  }

?>
