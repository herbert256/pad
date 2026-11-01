<?php

  // The mind map - subs/diagrams/types/mindmap.php: a subject in the middle and its branches
  // around it, the hierarchy read from the indentation as Mermaid's mindmap reads it.
  //
  //   PAD                                the root: the first line, the least indented
  //     Templates                        a branch: indented under its parent
  //       Tags
  //       Pipes
  //     Data
  //       db((Database))                 a shape: [square], (rounded), ((circle)),
  //                                      {{hexagon}}, )cloud(, ))bang((, an id before
  //                                      it or not
  //
  // The branches of the root are shared out over its two sides - the first ones right, the
  // rest left, as many leaves on each as can be - and each side is a tidy tree growing
  // outward, every branch of the root a colour of the chart palette that its twigs keep. A
  // branch is a box, a twig its text over a line; curves join them. The same lines draw the
  // same map: nothing is random.

  function padDiagramMindmap ( $source, $title ) {

    global $padCheckSyntax;

    $nodes  = $stack = $bad = [];
    $first  = TRUE;

    foreach ( preg_split ( '/\r?\n/', (string) $source ) as $number => $line ) {

      if ( preg_match ( '/^\s*%%/', $line ) or trim ( $line ) === '' )
        continue;

      $indent = strlen ( str_replace ( "\t", '    ', $line ) ) - strlen ( ltrim ( str_replace ( "\t", '    ', $line ) ) );
      $line   = trim ( $line );

      if ( $first and $line == 'mindmap' ) {
        $first = FALSE;
        continue;
      }

      $first = FALSE;

      // An icon and a class are Mermaid's, for its own styles: a line ::icon(...) is left
      // out, and so is :::name behind a node.

      if ( str_starts_with ( $line, '::icon(' ) )
        continue;

      $line = trim ( preg_replace ( '/\s*:::[\w\s-]+$/u', '', $line ) );

      list ( $text, $shape ) = padDiagramMindNode ( $line );

      while ( $stack and end ( $stack ) [0] >= $indent )
        array_pop ( $stack );

      if ( $nodes and ! $stack ) {
        $bad [] = [ $number + 1, $line ];
        continue;
      }

      $parent = $stack ? end ( $stack ) [1] : -1;
      $k      = count ( $nodes );

      $nodes [$k] = [ 'text' => $text, 'shape' => $shape, 'parent' => $parent, 'kids' => [],
                      'depth' => $parent < 0 ? 0 : $nodes [$parent] ['depth'] + 1 ];

      if ( $parent >= 0 )
        $nodes [$parent] ['kids'] [] = $k;

      $stack [] = [ $indent, $k ];

    }

    if ( $bad and $padCheckSyntax )
      padError ( 'the mind map has one root, and line ' . $bad [0] [0] . ": '" . padMakeSafe ( $bad [0] [1], 60 )
               . "' stands beside it - indent every line under the first" );

    if ( ! $nodes )
      return '';

    if ( count ( $nodes ) > 2000 ) {
      padError ( 'the mind map is too large to lay out: ' . count ( $nodes ) . ' nodes, 2000 at most - draw it in parts' );
      return '';
    }

    foreach ( $nodes as $k => $node )
      $nodes [$k] = padDiagramMindSize ( $node );

    // The branches of the root, right then left: the first ones right until that side holds
    // half the leaves, the rest left - read round the clock, so the left side from the
    // bottom up.

    $leaves = [];
    foreach ( array_reverse ( array_keys ( $nodes ) ) as $k )
      $leaves [$k] = $nodes [$k] ['kids'] ? array_sum ( array_map ( fn ( $kid ) => $leaves [$kid], $nodes [$k] ['kids'] ) ) : 1;

    $kids  = $nodes [0] ['kids'];
    $right = [];
    $have  = 0;

    foreach ( $kids as $kid )
      if ( ! $right or $have + $leaves [$kid] / 2 <= $leaves [0] / 2 ) {
        $right [] = $kid;
        $have    += $leaves [$kid];
      } else
        break;

    $left = array_reverse ( array_slice ( $kids, count ( $right ) ) );

    $nodes [0] ['x'] = 0;
    $nodes [0] ['y'] = 0;

    $extent = padDiagramMindExtents ( $nodes );

    foreach ( [ [ $right, 1 ], [ $left, -1 ] ] as list ( $side, $sign ) ) {

      $total = 0;
      foreach ( $side as $i => $kid )
        $total += $extent [$kid] + ( $i ? 18 : 0 );

      $y = -$total / 2;

      foreach ( $side as $kid ) {
        padDiagramMindPlace ( $nodes, $extent, $kid, $nodes [0] ['x'] + $sign * ( $nodes [0] ['w'] / 2 + 56 + $nodes [$kid] ['w'] / 2 ), $y + $extent [$kid] / 2, $sign );
        $y += $extent [$kid] + 18;
      }

    }

    // The colour of a branch, which its twigs keep.

    foreach ( $kids as $i => $kid )
      padDiagramMindPaint ( $nodes, $kid, $i % 8 + 1 );

    $minX = $minY = INF;
    $maxX = $maxY = -INF;

    foreach ( $nodes as $node ) {
      $minX = min ( $minX, $node ['x'] - $node ['w'] / 2 );
      $maxX = max ( $maxX, $node ['x'] + $node ['w'] / 2 );
      $minY = min ( $minY, $node ['y'] - $node ['h'] / 2 );
      $maxY = max ( $maxY, $node ['y'] + $node ['h'] / 2 );
    }

    $minX -= 12;
    $minY -= 12;

    $width  = (int) ceil ( $maxX - $minX + 12 );
    $height = (int) ceil ( $maxY - $minY + 12 );
    $X      = fn ( $x ) => padChartXY ( $x - $minX );
    $Y      = fn ( $y ) => padChartXY ( $y - $minY );

    // Said branch by branch: PAD: Templates, Data; Templates: Tags, Pipes.

    $desc = [];

    foreach ( $nodes as $node )
      if ( $node ['kids'] )
        $desc [] = padDiagramFlat ( $node ['text'] ) . ': ' . implode ( ', ', array_map ( fn ( $kid ) => padDiagramFlat ( $nodes [$kid] ['text'] ), $node ['kids'] ) );

    if ( ! $desc )
      $desc [] = padDiagramFlat ( $nodes [0] ['text'] );

    list ( $id, $svg ) = padChartOpen ( 'diagram', [ 'mindmap', $nodes ], $desc, $title, $width, $height, TRUE );

    $svg .= padDiagramStyle () . padDiagramMindStyle ();

    // The curves first, the nodes over their ends.

    foreach ( $nodes as $k => $node )
      if ( $node ['parent'] >= 0 ) {
        $from = padDiagramMindPort ( $nodes [ $node ['parent'] ], $node ['side'], TRUE );
        $to   = padDiagramMindPort ( $node, $node ['side'], FALSE );
        $half = ( $to [0] - $from [0] ) / 2;
        $svg .= '<path class="pd-mm-link pd-b' . $node ['slot'] . ' pd-mm-d' . min ( 3, $node ['depth'] ) . '" d="M' . $X ( $from [0] ) . ',' . $Y ( $from [1] )
              . 'C' . $X ( $from [0] + $half ) . ',' . $Y ( $from [1] ) . ' ' . $X ( $to [0] - $half ) . ',' . $Y ( $to [1] ) . ' ' . $X ( $to [0] ) . ',' . $Y ( $to [1] ) . '"/>';
      }

    foreach ( $nodes as $node )
      $svg .= padDiagramMindDraw ( $node, $X, $Y, $minX, $minY );

    return "$svg</svg>";

  }

  // A line's text and shape: id((text)), (text), [text], {{text}}, )text(, ))text(( or the
  // text alone. Quotes come off when they enclose the whole text, one quoted string -
  // id["text"] - as a flowchart's node has it: every quote at either end was taken, and
  // root((PAD "x")) was drawn as PAD "x, B(Press "OK") as Press "OK.

  function padDiagramMindNode ( $line ) {

    $shapes = [ '((' => [ '))', 'circle' ], '))' => [ '((', 'bang' ], '{{' => [ '}}', 'hexagon' ],
                '['  => [ ']',  'square' ], '('  => [ ')',  'rounded' ], ')' => [ '(', 'cloud' ] ];

    foreach ( $shapes as $open => list ( $close, $shape ) )
      if ( preg_match ( '/^[\p{L}\p{M}\p{N}_-]*' . preg_quote ( $open, '/' ) . '(.*)' . preg_quote ( $close, '/' ) . '$/su', $line, $m ) and trim ( $m [1] ) !== '' )
        return [ preg_match ( '/^"([^"]*)"$/u', trim ( $m [1] ), $quoted ) ? $quoted [1] : trim ( $m [1] ), $shape ];

    return [ $line, '' ];

  }

  // The size of a node: the root and the branches boxes round their text, a twig its text
  // over a line, a node with a shape of its own as large as that shape needs.

  function padDiagramMindSize ( $node ) {

    $node ['lines'] = padDiagramWrap ( $node ['text'], $node ['depth'] ? 22 : 18 );

    $textW = 0;
    foreach ( $node ['lines'] as $line )
      $textW = max ( $textW, padDiagramWidth ( $line ) * ( $node ['depth'] ? 1 : 1.2 ) );

    $textH = 16 * count ( $node ['lines'] );

    if ( $node ['shape'] === '' )
      $node ['shape'] = ( $node ['depth'] == 0 ) ? 'rounded' : ( $node ['depth'] == 1 ? 'box' : 'twig' );

    switch ( $node ['shape'] ) {
      case 'circle':  $d = max ( 48, hypot ( $textW, $textH ) + 18 ); return [ 'w' => $d, 'h' => $d ] + $node;
      case 'cloud':
      case 'bang':    return [ 'w' => $textW * 1.35 + 36, 'h' => $textH * 1.5 + 26 ] + $node;
      case 'hexagon': return [ 'w' => $textW + 44, 'h' => $textH + 18 ] + $node;
      case 'twig':    return [ 'w' => $textW + 12, 'h' => $textH + 8 ] + $node;
    }

    return [ 'w' => $textW + ( $node ['depth'] ? 24 : 36 ), 'h' => $textH + ( $node ['depth'] ? 14 : 22 ) ] + $node;

  }

  // How high each node's part of its side is: its own height, or its children's stacked -
  // by node, worked out once from the leaves up, as the tree's spans are: a node comes after
  // its parent in the list. Worked out again for every subtree at every level above it, a
  // chain of 2000 nested lines took half a second.

  function padDiagramMindExtents ( $nodes ) {

    $extent = [];

    foreach ( array_reverse ( array_keys ( $nodes ) ) as $k ) {

      $kids = 0;

      foreach ( $nodes [$k] ['kids'] as $i => $kid )
        $kids += $extent [$kid] + ( $i ? 8 : 0 );

      $extent [$k] = max ( $nodes [$k] ['h'], $kids );

    }

    return $extent;

  }

  function padDiagramMindPlace ( &$nodes, $extent, $k, $x, $y, $sign ) {

    $nodes [$k] ['x']    = $x;
    $nodes [$k] ['y']    = $y;
    $nodes [$k] ['side'] = $sign;

    $total = 0;
    foreach ( $nodes [$k] ['kids'] as $i => $kid )
      $total += $extent [$kid] + ( $i ? 8 : 0 );

    $at = $y - $total / 2;

    foreach ( $nodes [$k] ['kids'] as $kid ) {
      padDiagramMindPlace ( $nodes, $extent, $kid, $x + $sign * ( $nodes [$k] ['w'] / 2 + 36 + $nodes [$kid] ['w'] / 2 ), $at + $extent [$kid] / 2, $sign );
      $at += $extent [$kid] + 8;
    }

  }

  function padDiagramMindPaint ( &$nodes, $k, $slot ) {

    $nodes [$k] ['slot'] = $slot;

    foreach ( $nodes [$k] ['kids'] as $kid )
      padDiagramMindPaint ( $nodes, $kid, $slot );

  }

  // Where a curve leaves a node toward its children ($out) or comes into it: the root's
  // middle, under its fill; the side of a box; the end of a twig's line.

  function padDiagramMindPort ( $node, $sign, $out ) {

    if ( $node ['depth'] == 0 )
      return [ $node ['x'], $node ['y'] ];

    $x = $node ['x'] + ( $out ? $sign : -$sign ) * $node ['w'] / 2;

    return [ $x, $node ['y'] + ( $node ['shape'] == 'twig' ? $node ['h'] / 2 : 0 ) ];

  }

  function padDiagramMindDraw ( $node, $X, $Y, $minX, $minY ) {

    list ( $x, $y, $w, $h ) = [ $node ['x'], $node ['y'], $node ['w'], $node ['h'] ];

    $class = $node ['depth'] ? 'pd-mm-node pd-b' . $node ['slot'] : 'pd-mm-root';
    $text  = padDiagramText ( $node ['lines'], $x - $minX, $y - $minY, $node ['depth'] ? '' : 'pd-mm-top' );

    switch ( $node ['shape'] ) {

      case 'twig':
        return $text . '<path class="pd-mm-link pd-b' . $node ['slot'] . ' pd-mm-d' . min ( 3, $node ['depth'] ) . '" d="M' . $X ( $x - $w / 2 ) . ',' . $Y ( $y + $h / 2 ) . 'H' . $X ( $x + $w / 2 ) . '"/>';

      case 'circle':
        return '<circle class="' . $class . '" cx="' . $X ( $x ) . '" cy="' . $Y ( $y ) . '" r="' . padChartXY ( $w / 2 ) . '"/>' . $text;

      case 'cloud':
        return '<ellipse class="' . $class . '" cx="' . $X ( $x ) . '" cy="' . $Y ( $y ) . '" rx="' . padChartXY ( $w / 2 ) . '" ry="' . padChartXY ( $h / 2 ) . '"/>' . $text;

      case 'hexagon':
        return '<path class="' . $class . '" d="M' . $X ( $x - $w / 2 ) . ',' . $Y ( $y ) . 'L' . $X ( $x - $w / 2 + $h / 3 ) . ',' . $Y ( $y - $h / 2 )
             . 'H' . $X ( $x + $w / 2 - $h / 3 ) . 'L' . $X ( $x + $w / 2 ) . ',' . $Y ( $y ) . 'L' . $X ( $x + $w / 2 - $h / 3 ) . ',' . $Y ( $y + $h / 2 )
             . 'H' . $X ( $x - $w / 2 + $h / 3 ) . 'Z"/>' . $text;

      case 'bang':

        // A burst: twelve points round the text, every other one drawn in.

        $d = '';
        for ( $i = 0; $i < 24; $i++ ) {
          $a  = M_PI * 2 * $i / 24;
          $r  = ( $i % 2 ) ? 0.78 : 1;
          $d .= ( $i ? 'L' : 'M' ) . $X ( $x + cos ( $a ) * $w / 2 * $r ) . ',' . $Y ( $y + sin ( $a ) * $h / 2 * $r );
        }
        return '<path class="' . $class . "\" d=\"{$d}Z\"/>" . $text;

    }

    $rx = ( $node ['shape'] == 'square' ) ? 2 : ( $node ['shape'] == 'rounded' ? padChartXY ( $h / 2 ) : 6 );

    return '<rect class="' . $class . '" x="' . $X ( $x - $w / 2 ) . '" y="' . $Y ( $y - $h / 2 ) . '" width="' . padChartXY ( $w ) . '" height="' . padChartXY ( $h ) . "\" rx=\"$rx\"/>" . $text;

  }

  // The rules of the mind map: a branch takes its colour from the chart palette as
  // currentColor - its box a light fill and an outline of it, its curves and lines drawn in
  // it, thinner further out - and the root is drawn in the colour of the diagram's borders.

  function padDiagramMindStyle () {

    $css = '';

    for ( $slot = 1; $slot <= 8; $slot++ )
      $css .= ".pad-chart-diagram .pd-b$slot{color:var(--pad-chart-$slot)}";

    return '<style' . padStyleNonce () . '>' . $css
         . '.pad-chart-diagram .pd-mm-root{fill:var(--pad-diagram-border)}'
         . '.pad-chart-diagram .pd-mm-top{fill:var(--pad-chart-surface);font-size:14px;font-weight:700}'
         . '.pad-chart-diagram .pd-mm-node{fill:currentColor;fill-opacity:.14;stroke:currentColor;stroke-width:1.5}'
         . '.pad-chart-diagram .pd-mm-link{fill:none;stroke:currentColor;stroke-linecap:round}'
         . '.pad-chart-diagram .pd-mm-d1{stroke-width:3}.pad-chart-diagram .pd-mm-d2{stroke-width:2}.pad-chart-diagram .pd-mm-d3{stroke-width:1.5}'
         . '</style>';

  }

?>
