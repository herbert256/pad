<?php

  // Flows between the members of one group: {chart 'chord', data='migration', source='from',
  // target='to', value='people'}.
  //
  // A row is a flow from one node to another; source= and target= name the fields - else
  // the first two that are no number - and value= the amount, else the first numeric
  // field, else every row counts one. A flow of nothing, a negative one and one from a node
  // to itself are left out; flows between the same two nodes in one direction add up.
  //
  // The nodes are arcs around a circle, in the order they first appear, each as long as
  // all that flows out of it and into it, with a small gap between. A ribbon joins the two
  // ends of a flow through the middle, as wide as the flow at both ends and in the colour
  // of its source; at a node the ribbons lie in the order of the nodes they go to, so few
  // of them cross. The names stand outside the ring.

  function padChartChord ( $rows, $width, $height ) {

    global $padCheckSyntax;

    list ( $numbers, $names ) = padChartGuess ( $rows );

    $used   = [];
    $sField = padChartPick ( 'source', $names,   $used );
    $tField = padChartPick ( 'target', $names,   $used );
    $vField = padChartPick ( 'value',  $numbers, $used );

    if ( ( $sField === '' or $tField === '' ) and $padCheckSyntax )
      padError ( "the chord diagram has no field for the two ends of a flow - source='name', target='name'" );

    $title = (string) padTagParm ( 'title', 'Flows between ' . ( $sField ?: 'source' ) . ' and ' . ( $tField ?: 'target' ) );

    $links = [];

    foreach ( (array) $rows as $row ) {

      $row = padChartRow ( $row );
      $v   = ( $vField === '' ) ? 1 : ( $row [$vField] ?? NULL );
      $s   = padChartText ( $row, $sField );
      $t   = padChartText ( $row, $tField );

      if ( $s === '' or $t === '' or $s === $t or ! padChartFinite ( $v ) or $v <= 0 )
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

    // The nodes in the order they first appear, what flows out of each and into it.

    $nodes = $in = $out = [];

    foreach ( $links as list ( $s, $t, $v ) ) {
      foreach ( [ $s, $t ] as $node )
        if ( ! isset ( $nodes [$node] ) ) {
          $nodes [$node] = count ( $nodes );
          $in    [$node] = $out [$node] = 0;
        }
      $out [$s] += $v;
      $in  [$t] += $v;
    }

    $total = array_sum ( $out ) + array_sum ( $in );

    if ( ! is_finite ( $total ) or $total <= 0 )
      return '';

    $desc = [];
    foreach ( $links as list ( $s, $t, $v ) )
      $desc [] = "$s → $t: " . padChartNumber ( $v );

    list ( $id, $svg ) = padChartOpen ( 'chord', $links, $desc, $title, $width, $height );

    // The circle: as large as the chart allows with the names around it.

    $n       = count ( $nodes );
    $longest = 0;
    foreach ( $nodes as $node => $i )
      $longest = max ( $longest, padChartWidth ( padChartFit ( $node, 110 ) ) );

    $ring   = max ( 20, min ( ( $width - 2 * $longest - 28 ) / 2, ( $height - 44 ) / 2 ) );
    $thick  = max ( 4, min ( 14, $ring * 0.07 ) );
    $inner  = $ring - $thick;
    $cx     = $width / 2;
    $cy     = $height / 2;
    $gap    = ( $n > 1 ) ? min ( 0.04, 2 * M_PI * 0.25 / $n ) : 0;
    $scale  = ( 2 * M_PI - $n * $gap ) / $total;

    // Each node's arc, and in it the ends of its flows: ordered by how far clockwise the
    // other node lies, the nearest last, so a ribbon leaves on the side it goes to. Between
    // the same two nodes the flow out comes before the flow in, which nests the two.

    $start = [];
    $a     = $gap / 2;

    foreach ( $nodes as $node => $i ) {
      $start [$node] = $a;
      $a += ( $out [$node] + $in [$node] ) * $scale + $gap;
    }

    $ends = array_fill_keys ( array_keys ( $nodes ), [] );

    foreach ( $links as $l => list ( $s, $t, $v ) ) {
      $ends [$s] [] = [ ( $nodes [$t] - $nodes [$s] + $n ) % $n, 0, $l ];
      $ends [$t] [] = [ ( $nodes [$s] - $nodes [$t] + $n ) % $n, 1, $l ];
    }

    $from = $to = [];

    foreach ( $ends as $node => $list ) {
      usort ( $list, fn ( $p, $q ) => [ $q [0], $p [1], $p [2] ] <=> [ $p [0], $q [1], $q [2] ] );
      $at = $start [$node];
      foreach ( $list as list ( $far, $side, $l ) ) {
        $sweep = $links [$l] [2] * $scale;
        if ( $side == 0 )
          $from [$l] = [ $at, $at + $sweep ];
        else
          $to   [$l] = [ $at, $at + $sweep ];
        $at += $sweep;
      }
    }

    // The ribbons: along the inner edge of the ring at the source, through the middle to
    // the target, along it, and back through the middle.

    $at = fn ( $angle ) => padChartXY ( $cx + $inner * sin ( $angle ) ) . ',' . padChartXY ( $cy - $inner * cos ( $angle ) );
    $r  = padChartXY ( $inner );
    $c  = padChartXY ( $cx ) . ',' . padChartXY ( $cy );

    foreach ( $links as $l => list ( $s, $t, $v ) ) {

      list ( $s0, $s1 ) = $from [$l];
      list ( $t0, $t1 ) = $to   [$l];

      $d = 'M' . $at ( $s0 ) . "A$r,$r 0 " . ( $s1 - $s0 > M_PI ? 1 : 0 ) . ' 1 ' . $at ( $s1 )
         . "Q$c " . $at ( $t0 ) . "A$r,$r 0 " . ( $t1 - $t0 > M_PI ? 1 : 0 ) . ' 1 ' . $at ( $t1 )
         . "Q$c " . $at ( $s0 ) . 'Z';

      $svg .= '<path class="pc-mark pc-seg ' . padChartSlot ( $nodes [$s] ) . "\" fill-opacity=\"0.6\" d=\"$d\"><title>"
            . padChartAttr ( "$s → $t: " . padChartNumber ( $v ) ) . '</title></path>';

    }

    // The arcs of the nodes, and their names past the ring: on the right side read from
    // the ring outward, on the left toward it, at the top and bottom centred.

    foreach ( $nodes as $node => $i ) {

      $a0 = $start [$node];
      $a1 = $a0 + ( $out [$node] + $in [$node] ) * $scale;

      $svg .= '<path class="pc-mark ' . padChartSlot ( $i ) . '" d="' . padChartArc ( $cx, $cy, $inner + 2, $ring, $a0, $a1 ) . '"><title>'
            . padChartAttr ( $node . ': ' . padChartNumber ( $out [$node] ) . ' out, ' . padChartNumber ( $in [$node] ) . ' in' ) . '</title></path>';

      $mid    = ( $a0 + $a1 ) / 2;
      $sx     = sin ( $mid );
      $cy0    = cos ( $mid );
      $x      = $cx + ( $ring + 6 ) * $sx;
      $y      = $cy - ( $ring + 6 ) * $cy0 + 4;
      $anchor = ( $sx > 0.15 ) ? '' : ( ( $sx < -0.15 ) ? ' text-anchor="end"' : ' text-anchor="middle"' );

      if ( abs ( $sx ) <= 0.15 )
        $y += ( $cy0 > 0 ) ? -4 : 6;

      $svg .= '<text x="' . padChartXY ( $x ) . '" y="' . padChartXY ( $y ) . "\"$anchor>" . padChartAttr ( padChartFit ( $node, 110 ) ) . '</text>';

    }

    return "$svg</svg>";

  }

?>
