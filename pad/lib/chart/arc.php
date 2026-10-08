<?php

  // Connections along a line: {chart 'arc', data='scenes', source='a', target='b', value='scenes'}.
  //
  // A row is a link between two nodes; source= and target= name the fields - else the
  // first two that are no number. value= names a numeric field that makes a link thicker;
  // links between the same two nodes in one direction add up, and a link of a node to
  // itself only counts for its tooltip. The nodes stand on a line in the order they first
  // appear, each a dot as large as its number of links, its name under it - turned when
  // the names would touch; a link is a half circle above the line, flattened when the
  // widest would not fit the height.

  function padChartArcDiagram ( $rows, $width, $height ) {

    global $padCheckSyntax;

    list ( $numbers, $names ) = padChartGuess ( $rows );

    $used   = [];
    $sField = padChartPick ( 'source', $names, $used );
    $tField = padChartPick ( 'target', $names, $used );
    $vField = (string) padTagParm ( 'value' );

    if ( ( $sField === '' or $tField === '' ) and $padCheckSyntax )
      padError ( "the arc diagram has no field for the two ends of a link - source='name', target='name'" );

    $title = (string) padTagParm ( 'title', 'Links between ' . ( $sField ?: 'source' ) . ' and ' . ( $tField ?: 'target' ) );

    $links = $nodes = [];

    foreach ( (array) $rows as $row ) {

      $row = padChartRow ( $row );
      $s   = padChartText ( $row, $sField );
      $t   = padChartText ( $row, $tField );
      $v   = ( $vField === '' ) ? 1 : ( $row [$vField] ?? NULL );

      if ( $s === '' or $t === '' or ! padChartFinite ( $v ) or $v < 0 )
        continue;

      foreach ( [ $s, $t ] as $node )
        if ( ! isset ( $nodes [$node] ) )
          $nodes [$node] = [ count ( $nodes ), 0 ];

      $key = serialize ( [ $s, $t ] );

      if ( isset ( $links [$key] ) )
        $links [$key] [2] += $v;
      else {
        $links [$key] = [ $s, $t, $v + 0 ];
        if ( $s !== $t ) {
          $nodes [$s] [1]++;
          $nodes [$t] [1]++;
        }
      }

    }

    $links = array_values ( $links );

    if ( ! $links )
      return '';

    $desc = [ count ( $nodes ) . ( count ( $nodes ) == 1 ? ' node, ' : ' nodes, ' ) . count ( $links ) . ( count ( $links ) == 1 ? ' link' : ' links' ) ];
    foreach ( $links as list ( $s, $t, $v ) )
      $desc [] = "$s → $t" . ( $vField !== '' ? ': ' . padChartNumber ( $v ) : '' );

    list ( $id, $svg ) = padChartOpen ( 'arc', $links, $desc, $title, $width, $height );

    // The names: level when they fit beside each other, else turned 45 degrees, else 90 -
    // the room under the line and at the left follows the turn.

    $n       = count ( $nodes );
    $longest = 0;
    foreach ( $nodes as $node => $i )
      $longest = max ( $longest, padChartWidth ( padChartFit ( $node, 110 ) ) );

    $step = ( $width - 32 ) / max ( 1, $n - 1 );

    if ( $n == 1 or $step >= $longest + 8 ) {
      $turn   = 0;
      $left   = max ( 16, min ( $longest / 2 + 4, $width / 4 ) );
      $right  = $left;
      $bottom = 30;
    } elseif ( $step >= 14 ) {
      $turn   = 45;
      $left   = max ( 16, $longest * 0.71 + 4 );
      $right  = 16;
      $bottom = $longest * 0.71 + 30;
    } else {
      $turn   = 90;
      $left   = $right = 16;
      $bottom = $longest + 30;
    }

    $bottom = min ( $bottom, $height * 0.45 );
    $base   = $height - $bottom;
    $step   = ( $n > 1 ) ? ( $width - $left - $right ) / ( $n - 1 ) : 0;
    $x      = fn ( $node ) => ( $n > 1 ) ? $left + $nodes [$node] [0] * $step : $width / 2;
    $dot    = fn ( $node ) => min ( 9, max ( 3, min ( $step / 2 - 1, 3 + 1.4 * sqrt ( $nodes [$node] [1] ) ) ) );

    // The arcs: a half circle over the two nodes, its height scaled down alike for all
    // when the widest would rise past the top.

    $widest = 0;
    foreach ( $links as list ( $s, $t ) )
      $widest = max ( $widest, abs ( $x ( $t ) - $x ( $s ) ) / 2 );

    $flat = ( $widest > 0 ) ? min ( 1, ( $base - 12 ) / $widest ) : 1;
    $vMax = max ( array_column ( $links, 2 ) );

    foreach ( $links as list ( $s, $t, $v ) ) {

      if ( $s === $t )
        continue;

      $x0 = min ( $x ( $s ), $x ( $t ) );
      $x1 = max ( $x ( $s ), $x ( $t ) );
      $rx = ( $x1 - $x0 ) / 2;
      $ry = $rx * $flat;

      $stroke = ( $vField !== '' and $vMax > 0 ) ? 1 + 5 * $v / $vMax : 1.5;

      $svg .= '<path class="pc-edge" d="M' . padChartXY ( $x0 ) . ',' . padChartXY ( $base ) . 'A' . padChartXY ( $rx ) . ',' . padChartXY ( $ry ) . ' 0 0 1 ' . padChartXY ( $x1 ) . ',' . padChartXY ( $base )
            . '" stroke-width="' . padChartXY ( $stroke ) . '"><title>'
            . padChartAttr ( "$s → $t" . ( $vField !== '' ? ': ' . padChartNumber ( $v ) : '' ) ) . '</title></path>';

    }

    // The line, the nodes on it, and their names.

    if ( $n > 1 )
      $svg .= '<line class="pc-grid" x1="' . padChartXY ( $left ) . '" x2="' . padChartXY ( $width - $right ) . '" y1="' . padChartXY ( $base ) . '" y2="' . padChartXY ( $base ) . '"/>';

    $room = ( $turn == 0 ) ? max ( $step - 4, $longest ) : ( $turn == 45 ? ( $bottom - 24 ) / 0.71 : $bottom - 24 );

    foreach ( $nodes as $node => list ( $i, $k ) ) {

      $nx = $x ( $node );
      $r  = $dot ( $node );
      $ty = $base + $r + 12;

      $svg .= '<circle class="pc-mark pc-ring pc-c1" cx="' . padChartXY ( $nx ) . '" cy="' . padChartXY ( $base ) . '" r="' . padChartXY ( $r ) . '"><title>'
            . padChartAttr ( $node . ': ' . $k . ( $k == 1 ? ' link' : ' links' ) ) . '</title></circle>';

      $name = padChartAttr ( padChartFit ( $node, min ( 110, $room ) ) );

      if ( $turn == 0 )
        $svg .= '<text x="' . padChartXY ( $nx ) . '" y="' . padChartXY ( $ty ) . "\" text-anchor=\"middle\">$name</text>";
      else
        $svg .= '<text x="' . padChartXY ( $nx + 3 ) . '" y="' . padChartXY ( $ty - 6 ) . '" text-anchor="end" transform="rotate(-' . $turn . ' ' . padChartXY ( $nx + 3 ) . ' ' . padChartXY ( $ty - 6 ) . ")\">$name</text>";

    }

    return "$svg</svg>";

  }

?>
