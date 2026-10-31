<?php

  // The tree - diagrams/types/tree.php: a hierarchy read from the indentation, drawn top
  // down (or left to right) as boxes joined by lines with rounded turns - an organisation, a
  // file tree, the parts of a product.
  //
  //   Company                            a root: a line at the least indentation; a tree
  //     Sales                            can have several, side by side
  //       North
  //       South
  //     Engineering
  //
  // Each node is centred over its children and each subtree takes the room of its widest
  // row, so the same lines draw the same tree, and no two boxes touch.

  function padDiagramTree ( $source, $direction, $title ) {

    $nodes = $stack = [];

    foreach ( preg_split ( '/\r?\n/', (string) $source ) as $line ) {

      if ( preg_match ( '/^\s*%%/', $line ) or trim ( $line ) === '' )
        continue;

      $line   = str_replace ( "\t", '    ', $line );
      $indent = strlen ( $line ) - strlen ( ltrim ( $line ) );

      while ( $stack and end ( $stack ) [0] >= $indent )
        array_pop ( $stack );

      $parent = $stack ? end ( $stack ) [1] : -1;
      $k      = count ( $nodes );

      $nodes [$k] = [ 'text' => trim ( $line ), 'parent' => $parent, 'kids' => [],
                      'depth' => $parent < 0 ? 0 : $nodes [$parent] ['depth'] + 1 ];

      if ( $parent >= 0 )
        $nodes [$parent] ['kids'] [] = $k;

      $stack [] = [ $indent, $k ];

    }

    if ( ! $nodes )
      return '';

    if ( count ( $nodes ) > 2000 ) {
      padError ( 'the tree is too large to lay out: ' . count ( $nodes ) . ' nodes, 2000 at most - draw it in parts' );
      return '';
    }

    if ( $direction === '' or $direction == 'TD' )
      $direction = 'TB';

    $vertical = in_array ( $direction, [ 'TB', 'BT' ] );

    // The size of a box from its text; across is the axis the children lie side by side on,
    // along the one the levels follow.

    foreach ( $nodes as $k => $node ) {
      $lines = padDiagramWrap ( $node ['text'], $vertical ? 18 : 28 );
      $textW = 0;
      foreach ( $lines as $line )
        $textW = max ( $textW, padDiagramWidth ( $line ) );
      $nodes [$k] += [ 'lines' => $lines, 'w' => max ( 60, $textW + 24 ), 'h' => 16 * count ( $lines ) + 16 ];
      $nodes [$k] ['across'] = $vertical ? $nodes [$k] ['w'] : $nodes [$k] ['h'];
      $nodes [$k] ['along']  = $vertical ? $nodes [$k] ['h'] : $nodes [$k] ['w'];
    }

    // Each level as deep as its deepest box, the gap between two levels room for the turn.

    $depth = [];
    foreach ( $nodes as $node )
      $depth [ $node ['depth'] ] = max ( $depth [ $node ['depth'] ] ?? 0, $node ['along'] );

    $level = [];
    $at    = 0;
    foreach ( $depth as $d => $size ) {
      $level [$d] = $at + $size / 2;
      $at        += $size + ( $vertical ? 40 : 48 );
    }

    // Across: a subtree is as wide as its box or its children side by side, whichever is
    // wider, its box centred over them.

    $span = [];
    foreach ( array_reverse ( array_keys ( $nodes ) ) as $k ) {
      $kids = 0;
      foreach ( $nodes [$k] ['kids'] as $i => $kid )
        $kids += $span [$kid] + ( $i ? 16 : 0 );
      $span [$k] = max ( $nodes [$k] ['across'], $kids );
    }

    $place = function ( $k, $from ) use ( &$place, &$nodes, $span, $level ) {
      $nodes [$k] ['cross'] = $from + $span [$k] / 2;
      $nodes [$k] ['main']  = $level [ $nodes [$k] ['depth'] ];
      $kids = 0;
      foreach ( $nodes [$k] ['kids'] as $i => $kid )
        $kids += $span [$kid] + ( $i ? 16 : 0 );
      $at = $from + ( $span [$k] - $kids ) / 2;
      foreach ( $nodes [$k] ['kids'] as $kid ) {
        $place ( $kid, $at );
        $at += $span [$kid] + 16;
      }
    };

    $from = 0;
    foreach ( $nodes as $k => $node )
      if ( $node ['parent'] < 0 ) {
        $place ( $k, $from );
        $from += $span [$k] + 32;
      }

    // Turned to the direction asked: BT and RL mirror the levels.

    $along = $at - ( $vertical ? 40 : 48 );

    foreach ( $nodes as $k => $node ) {
      $main = in_array ( $direction, [ 'BT', 'RL' ] ) ? $along - $node ['main'] : $node ['main'];
      $nodes [$k] ['x'] = ( $vertical ? $node ['cross'] : $main ) + 12;
      $nodes [$k] ['y'] = ( $vertical ? $main : $node ['cross'] ) + 12;
    }

    $width  = (int) ceil ( ( $vertical ? $from - 32 : $along ) + 24 );
    $height = (int) ceil ( ( $vertical ? $along : $from - 32 ) + 24 );
    $X      = fn ( $v ) => padChartXY ( $v );

    $desc = [];
    foreach ( $nodes as $node )
      if ( $node ['kids'] )
        $desc [] = padDiagramFlat ( $node ['text'] ) . ': ' . implode ( ', ', array_map ( fn ( $kid ) => padDiagramFlat ( $nodes [$kid] ['text'] ), $node ['kids'] ) );
      elseif ( $node ['parent'] < 0 )
        $desc [] = padDiagramFlat ( $node ['text'] );

    list ( $id, $svg ) = padChartOpen ( 'diagram', [ 'tree', $nodes, $direction ], $desc, $title, $width, $height, FALSE );

    $svg .= padDiagramStyle () . padDiagramTreeStyle ();

    // The lines: from a box to the middle of the gap, across, and on to each child - one
    // path per child, the shared part drawn over itself.

    $sign = in_array ( $direction, [ 'BT', 'RL' ] ) ? -1 : 1;

    foreach ( $nodes as $node )
      if ( $node ['parent'] >= 0 ) {
        $up   = $nodes [ $node ['parent'] ];
        if ( $vertical ) {
          $a   = [ $up ['x'], $up ['y'] + $sign * $up ['h'] / 2 ];
          $b   = [ $node ['x'], $node ['y'] - $sign * $node ['h'] / 2 ];
          $mid = $b [1] - $sign * 20;
          $points = [ $a, [ $a [0], $mid ], [ $b [0], $mid ], $b ];
        } else {
          $a   = [ $up ['x'] + $sign * $up ['w'] / 2, $up ['y'] ];
          $b   = [ $node ['x'] - $sign * $node ['w'] / 2, $node ['y'] ];
          $mid = $b [0] - $sign * 24;
          $points = [ $a, [ $mid, $a [1] ], [ $mid, $b [1] ], $b ];
        }
        if ( abs ( $a [ $vertical ? 0 : 1 ] - $b [ $vertical ? 0 : 1 ] ) < 0.5 )
          $points = [ $a, $b ];
        $svg .= '<path class="pd-edge" d="' . padDiagramPath ( $points, 'orthogonal', $vertical, $X, $X ) . '"/>';
      }

    foreach ( $nodes as $node ) {
      $class = $node ['parent'] < 0 ? 'pd-node pd-root' : ( $node ['kids'] ? 'pd-node' : 'pd-node pd-leaf' );
      $svg  .= '<rect class="' . $class . '" x="' . $X ( $node ['x'] - $node ['w'] / 2 ) . '" y="' . $X ( $node ['y'] - $node ['h'] / 2 ) . '" width="'
             . $X ( $node ['w'] ) . '" height="' . $X ( $node ['h'] ) . '" rx="4"/>'
             . padDiagramText ( $node ['lines'], $node ['x'], $node ['y'], $node ['parent'] < 0 ? 'pd-root-text' : '' );
    }

    return "$svg</svg>";

  }

  // The rules of the tree: a root in the colour of the borders, its text on it in the colour
  // of the background; a leaf on the background. The rules of every diagram on a page are
  // the page's, the last one's winning where two weigh the same: written .pd-root alone, the
  // .pd-node of a diagram further down the page painted the roots over in a node's colour.

  function padDiagramTreeStyle () {

    return '<style' . padStyleNonce () . '>'
         . '.pad-chart-diagram .pd-node.pd-root{fill:var(--pad-diagram-border)}'
         . '.pad-chart-diagram .pd-root-text{fill:var(--pad-chart-surface);font-weight:700}'
         . '.pad-chart-diagram .pd-node.pd-leaf{fill:var(--pad-chart-surface)}'
         . '</style>';

  }

?>
