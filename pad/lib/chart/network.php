<?php

  // Connections: {chart 'network', data='friends', source='from', target='to'}.
  //
  // A row is a link between two nodes; source= and target= name the fields - else the
  // first two that are no number. value= names a numeric field that makes a link thicker;
  // arrows marks the direction of each link. layout='force' (the default) lets linked
  // nodes pull together and all nodes push apart - computed here, from a circle, so the
  // same data draws the same picture - and layout='radial' sets the nodes on a circle. A
  // node is larger the more links it has; up to forty nodes are named beside them.

  function padChartNetwork ( $rows, $width, $height ) {

    global $padCheckSyntax;

    list ( $numbers, $names ) = padChartGuess ( $rows );

    $used   = [];
    $sField = padChartPick ( 'source', $names, $used );
    $tField = padChartPick ( 'target', $names, $used );
    $vField = (string) padTagParm ( 'value' );
    $layout = strtolower ( (string) padTagParm ( 'layout', 'force' ) );
    $arrows = padTagParm ( 'arrows', FALSE );
    $arrows = ( $arrows !== FALSE and $arrows !== '' and $arrows !== 0 and $arrows !== '0' );

    if ( ( $sField === '' or $tField === '' ) and $padCheckSyntax )
      padError ( "the network has no field for the two ends of a link - source='name', target='name'" );

    if ( ! in_array ( $layout, [ 'force', 'radial' ] ) ) {
      if ( $padCheckSyntax )
        padError ( "the network has no layout named '" . padMakeSafe ( $layout, 20 ) . "' - force or radial" );
      $layout = 'force';
    }

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

      if ( $s !== $t ) {
        $nodes [$s] [1]++;
        $nodes [$t] [1]++;
      }

      $key = serialize ( [ $s, $t ] );

      if ( isset ( $links [$key] ) )
        $links [$key] [2] += $v;
      else
        $links [$key] = [ $s, $t, $v + 0 ];

    }

    $links = array_values ( $links );

    if ( ! $links )
      return '';

    $desc = [ count ( $nodes ) . ( count ( $nodes ) == 1 ? ' node, ' : ' nodes, ' ) . count ( $links ) . ( count ( $links ) == 1 ? ' link' : ' links' ) ];
    foreach ( $links as list ( $s, $t, $v ) )
      $desc [] = "$s → $t" . ( $vField !== '' ? ': ' . padChartNumber ( $v ) : '' );

    list ( $id, $svg ) = padChartOpen ( 'network', [ $links, $layout, $arrows ], $desc, $title, $width, $height );

    $n     = count ( $nodes );
    $named = ( $n <= 40 );
    $index = array_keys ( $nodes );
    $pos   = [];

    foreach ( $index as $i => $node ) {
      $a = 2 * M_PI * $i / $n;
      $pos [$i] = [ sin ( $a ), -cos ( $a ) ];
    }

    $pairs = [];
    foreach ( $links as list ( $s, $t ) )
      if ( $s !== $t )
        $pairs [] = [ $nodes [$s] [0], $nodes [$t] [0] ];

    if ( $layout == 'force' and $n > 2 and $n <= 300 )
      $pos = padChartForce ( $pos, $pairs );

    // Fit the layout into the chart, keeping room for the node and its name.

    $longest = 0;
    if ( $named )
      foreach ( $index as $node )
        $longest = max ( $longest, padChartWidth ( padChartFit ( $node, 120 ) ) );

    $xs = array_column ( $pos, 0 );
    $ys = array_column ( $pos, 1 );
    $x0 = min ( $xs );  $xw = max ( $xs ) - $x0 ?: 1;
    $y0 = min ( $ys );  $yw = max ( $ys ) - $y0 ?: 1;

    $padLeft  = ( $layout == 'radial' and $named ) ? $longest + 18 : 16;
    $padRight = $named ? $longest + 18 : 16;
    $plotW    = max ( 1, $width  - $padLeft - $padRight );
    $padTop   = ( $layout == 'radial' and $named ) ? 30 : 16;
    $plotH    = max ( 1, $height - 2 * $padTop );

    $px = fn ( $i ) => $padLeft + ( $pos [$i] [0] - $x0 ) / $xw * $plotW;
    $py = fn ( $i ) => $padTop + ( $pos [$i] [1] - $y0 ) / $yw * $plotH;
    $pr = fn ( $node ) => min ( 12, 4 + 1.5 * sqrt ( $nodes [$node] [1] ) );

    if ( $arrows )
      $svg .= "<defs><marker id=\"$id-arrow\" viewBox=\"0 0 10 10\" refX=\"9\" refY=\"5\" markerWidth=\"7\" markerHeight=\"7\" orient=\"auto-start-reverse\">"
            . '<path class="pc-arrow" d="M0,0L10,5L0,10Z"/></marker></defs>';

    $vMax = max ( array_column ( $links, 2 ) );

    foreach ( $links as list ( $s, $t, $v ) ) {

      if ( $s === $t )
        continue;

      $a  = $nodes [$s] [0];
      $b  = $nodes [$t] [0];
      $ax = $px ( $a );  $ay = $py ( $a );
      $bx = $px ( $b );  $by = $py ( $b );

      if ( $arrows and ( $length = hypot ( $bx - $ax, $by - $ay ) ) > 0 ) {
        $cut = min ( $length / 2, $pr ( $t ) + 2 );
        $bx -= ( $bx - $ax ) / $length * $cut;
        $by -= ( $by - $ay ) / $length * $cut;
      }

      $stroke = ( $vField !== '' and $vMax > 0 ) ? 1 + 3 * $v / $vMax : 1.5;

      $svg .= '<line class="pc-edge" x1="' . padChartXY ( $ax ) . '" y1="' . padChartXY ( $ay ) . '" x2="' . padChartXY ( $bx ) . '" y2="' . padChartXY ( $by ) . '" stroke-width="' . padChartXY ( $stroke ) . '"'
            . ( $arrows ? " marker-end=\"url(#$id-arrow)\"" : '' ) . '><title>'
            . padChartAttr ( "$s → $t" . ( $vField !== '' ? ': ' . padChartNumber ( $v ) : '' ) ) . '</title></line>';

    }

    foreach ( $index as $i => $node ) {

      $x = $px ( $i );
      $y = $py ( $i );
      $r = $pr ( $node );
      $k = $nodes [$node] [1];

      $svg .= '<circle class="pc-mark pc-ring pc-c1" cx="' . padChartXY ( $x ) . '" cy="' . padChartXY ( $y ) . '" r="' . padChartXY ( $r ) . '"><title>'
            . padChartAttr ( $node . ': ' . $k . ( $k == 1 ? ' link' : ' links' ) ) . '</title></circle>';

      if ( ! $named )
        continue;

      if ( $layout == 'radial' ) {
        $dx     = $pos [$i] [0];
        $anchor = ( $dx > 0.1 ) ? '' : ( ( $dx < -0.1 ) ? ' text-anchor="end"' : ' text-anchor="middle"' );
        $lx     = $x + ( $dx > 0.1 ? $r + 4 : ( $dx < -0.1 ? -$r - 4 : 0 ) );
        $ly     = $y + ( abs ( $dx ) <= 0.1 ? ( $pos [$i] [1] < 0 ? -$r - 4 : $r + 12 ) : 4 );
      } else {
        $anchor = '';
        $lx     = $x + $r + 4;
        $ly     = $y + 4;
      }

      $svg .= '<text x="' . padChartXY ( $lx ) . '" y="' . padChartXY ( $ly ) . "\"$anchor>" . padChartAttr ( padChartFit ( $node, 120 ) ) . '</text>';

    }

    return "$svg</svg>";

  }

  // A force-directed layout (Fruchterman and Reingold): every pair of nodes pushes apart,
  // every link pulls its two ends together, a little gravity keeps loose parts near the
  // middle, and the step each node may take cools down to nothing. The work grows with the
  // square of the nodes, so the rounds are fewer for a larger network.

  function padChartForce ( $pos, $pairs ) {

    $n      = count ( $pos );
    $k      = sqrt ( 4 / $n );
    $rounds = max ( 30, min ( 300, (int) ( 3e6 / ( $n * $n ) ) ) );

    for ( $round = 0; $round < $rounds; $round++ ) {

      $heat = 0.2 * ( 1 - $round / $rounds );
      $move = array_fill ( 0, $n, [ 0, 0 ] );

      for ( $i = 0; $i < $n; $i++ )
        for ( $j = $i + 1; $j < $n; $j++ ) {
          $dx = $pos [$i] [0] - $pos [$j] [0];
          $dy = $pos [$i] [1] - $pos [$j] [1];
          $d2 = max ( 1e-6, $dx * $dx + $dy * $dy );
          $f  = $k * $k / $d2;
          $move [$i] [0] += $dx * $f;  $move [$i] [1] += $dy * $f;
          $move [$j] [0] -= $dx * $f;  $move [$j] [1] -= $dy * $f;
        }

      foreach ( $pairs as list ( $a, $b ) ) {
        $dx = $pos [$a] [0] - $pos [$b] [0];
        $dy = $pos [$a] [1] - $pos [$b] [1];
        $f  = sqrt ( $dx * $dx + $dy * $dy ) / $k;
        $move [$a] [0] -= $dx * $f;  $move [$a] [1] -= $dy * $f;
        $move [$b] [0] += $dx * $f;  $move [$b] [1] += $dy * $f;
      }

      for ( $i = 0; $i < $n; $i++ ) {
        $mx = $move [$i] [0] - $pos [$i] [0] * $k * 0.5;
        $my = $move [$i] [1] - $pos [$i] [1] * $k * 0.5;
        $d  = sqrt ( $mx * $mx + $my * $my );
        if ( $d > 0 ) {
          $step = min ( $d, $heat );
          $pos [$i] [0] += $mx / $d * $step;
          $pos [$i] [1] += $my / $d * $step;
        }
      }

    }

    return $pos;

  }

?>
