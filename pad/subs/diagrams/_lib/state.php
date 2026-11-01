<?php

  // The state diagram - subs/diagrams/types/state.php: the states of a machine and the transitions
  // between them, written as Mermaid's stateDiagram-v2 writes them and laid out in layers as a
  // flowchart is (padDiagramGraph).
  //
  //   [*] --> Idle                       the start: [*] on the left of an arrow
  //   Idle --> Running : start           a transition, its label after the colon
  //   Running --> [*]                    the end: [*] on the right
  //   state "Waiting for payment" as W   a state with a name of more than one word
  //   W : entry / send the invoice       a line of description under the name
  //   state Check <<choice>>             a choice (a small diamond), <<fork>>, <<join>>
  //   direction LR                       the direction, when the tag gives none
  //
  // There is one start and one end, as Mermaid has them on the top level; a state inside
  // another - state X { ... } - and a note are lines it cannot read.

  function padDiagramState ( $source, $direction, $edgeForm, $title ) {

    global $padCheckSyntax;

    $id      = '[\p{L}\p{M}\p{N}_]+';
    $nodes   = $edges = $bad = $about = [];
    $written = '';
    $first   = TRUE;

    // A state is named the first time a line mentions it; a later line can give it a name to
    // show, a description or a kind. The start and the end are ids no line can write.

    $state = function ( $name, $side = 'from' ) use ( &$nodes ) {
      if ( $name == '[*]' )
        $name = ( $side == 'from' ) ? '(start)' : '(end)';
      $nodes [$name] ??= [ 'text'  => [ '(start)' => 'Start', '(end)' => 'End' ] [$name] ?? $name,
                           'shape' => [ '(start)' => 'start', '(end)' => 'end' ] [$name] ?? 'state', 'about' => [] ];
      return $name;
    };

    foreach ( preg_split ( '/\r?\n/', (string) $source ) as $number => $line ) {

      $line = preg_match ( '/^\s*%%/', $line ) ? '' : trim ( $line );

      if ( $line === '' )
        continue;

      if ( $first and preg_match ( '/^stateDiagram(-v2)?$/', $line ) ) {
        $first = FALSE;
        continue;
      }

      $first = FALSE;

      if ( preg_match ( '/^direction\s+(TB|TD|BT|LR|RL)$/i', $line, $m ) )
        $written = strtoupper ( $m [1] );
      elseif ( preg_match ( "/^(\[\*\]|$id)\s*-->\s*(\[\*\]|$id)\s*(?::\s*(.*))?$/u", $line, $m ) ) {
        $a        = $state ( $m [1], 'from' );
        $b        = $state ( $m [2], 'to' );
        $edges [] = [ $a, $b, padDiagramFlat ( $m [3] ?? '' ), 'solid', TRUE ];
      } elseif ( preg_match ( "/^state\s+\"([^\"]*)\"\s+as\s+($id)$/u", $line, $m ) )
        $nodes [ $state ( $m [2] ) ] ['text'] = padDiagramFlat ( $m [1] );
      elseif ( preg_match ( "/^state\s+($id)(?:\s+<<(choice|fork|join)>>)?$/iu", $line, $m ) ) {
        $name = $state ( $m [1] );
        if ( isset ( $m [2] ) )
          $nodes [$name] ['shape'] = ( strtolower ( $m [2] ) == 'choice' ) ? 'choice' : 'fork';
      } elseif ( preg_match ( "/^($id)\s*:\s*(.+)$/u", $line, $m ) )
        $nodes [ $state ( $m [1] ) ] ['about'] [] = padDiagramFlat ( $m [2] );
      else
        $bad [] = [ $number + 1, $line ];

    }

    if ( $bad and $padCheckSyntax )
      padError ( 'the state diagram cannot read line ' . $bad [0] [0] . ": '" . padMakeSafe ( $bad [0] [1], 60 ) . "' - write [*] --> A, A --> B : label, state \"Name\" as A, A : description or state C <<choice>>" );

    if ( ! $nodes )
      return '';

    if ( $direction === '' )
      $direction = $written !== '' ? $written : 'TB';

    if ( $direction == 'TD' )
      $direction = 'TB';

    $vertical = in_array ( $direction, [ 'TB', 'BT' ] );

    foreach ( $nodes as $name => $node )
      $nodes [$name] = padDiagramStateSize ( $node, $vertical );

    $desc = [];

    foreach ( $edges as list ( $a, $b, $label ) )
      $desc [] = $nodes [$a] ['text'] . ' → ' . $nodes [$b] ['text'] . ( $label !== '' ? " ($label)" : '' );

    $linked = [];
    foreach ( $edges as list ( $a, $b ) )
      $linked [$a] = $linked [$b] = TRUE;

    foreach ( $nodes as $name => $node )
      if ( $node ['about'] or ! isset ( $linked [$name] ) )
        $desc [] = $node ['text'] . ( $node ['about'] ? ': ' . implode ( ', ', $node ['about'] ) : '' );

    return padDiagramGraph ( $nodes, $edges, $desc, $direction, $edgeForm, $title, padDiagramStateStyle () );

  }

  // The size of a state: a rounded box round its name, and its description under a line; the
  // start a dot, the end a ring round one, a choice a small diamond, a fork or a join a bar
  // across the flow.

  function padDiagramStateSize ( $node, $vertical ) {

    $node ['lines'] = $node ['about'] ? [] : padDiagramWrap ( $node ['text'] );

    switch ( $node ['shape'] ) {
      case 'start':  return [ 'w' => 18, 'h' => 18 ] + $node;
      case 'end':    return [ 'w' => 22, 'h' => 22 ] + $node;
      case 'choice': return [ 'w' => 28, 'h' => 28 ] + $node;
      case 'fork':   return ( $vertical ? [ 'w' => 80, 'h' => 8 ] : [ 'w' => 8, 'h' => 80 ] ) + $node;
    }

    $name  = padDiagramWrap ( $node ['text'] );
    $about = [];

    foreach ( $node ['about'] as $text )
      foreach ( padDiagramWrap ( $text, 32 ) as $one )
        $about [] = $one;

    $textW = 0;
    foreach ( array_merge ( $name, $about ) as $line )
      $textW = max ( $textW, padDiagramWidth ( $line ) );

    $node ['name']  = $name;
    $node ['lines'] = $about;
    $node ['w']     = max ( 70, $textW + 28 );
    $node ['h']     = $about ? 16 * count ( $name ) + 14 + 16 * count ( $about ) + 8 : max ( 34, 16 * count ( $name ) + 16 );

    return $node;

  }

  function padDiagramDrawState ( $node, $X, $Y, $minX, $minY ) {

    list ( $x, $y, $w, $h ) = [ $node ['x'], $node ['y'], $node ['w'], $node ['h'] ];

    $svg = '<rect class="pd-node" x="' . $X ( $x - $w / 2 ) . '" y="' . $Y ( $y - $h / 2 ) . '" width="' . padChartXY ( $w ) . '" height="' . padChartXY ( $h ) . '" rx="10"/>';

    if ( ! $node ['lines'] )
      return $svg . padDiagramText ( $node ['name'], $x - $minX, $y - $minY );

    $line = $y - $h / 2 + 16 * count ( $node ['name'] ) + 10;

    return $svg . padDiagramText ( $node ['name'], $x - $minX, $y - $minY - $h / 2 + 5 + 8 * count ( $node ['name'] ) )
         . '<path class="pd-line" d="M' . $X ( $x - $w / 2 ) . ',' . $Y ( $line ) . 'H' . $X ( $x + $w / 2 ) . '"/>'
         . padDiagramText ( $node ['lines'], $x - $minX, $line - $minY + 6 + 8 * count ( $node ['lines'] ), 'pd-desc' );

  }

  function padDiagramDrawStart ( $node, $X, $Y ) {

    return '<circle class="pd-start" cx="' . $X ( $node ['x'] ) . '" cy="' . $Y ( $node ['y'] ) . '" r="' . padChartXY ( $node ['w'] / 2 ) . '"/>';

  }

  function padDiagramDrawEnd ( $node, $X, $Y ) {

    return '<circle class="pd-stop" cx="' . $X ( $node ['x'] ) . '" cy="' . $Y ( $node ['y'] ) . '" r="' . padChartXY ( $node ['w'] / 2 ) . '"/>'
         . '<circle class="pd-start" cx="' . $X ( $node ['x'] ) . '" cy="' . $Y ( $node ['y'] ) . '" r="' . padChartXY ( $node ['w'] / 2 - 5 ) . '"/>';

  }

  function padDiagramDrawChoice ( $node, $X, $Y ) {

    list ( $x, $y, $w, $h ) = [ $node ['x'], $node ['y'], $node ['w'], $node ['h'] ];

    return '<path class="pd-node pd-diamond" d="M' . $X ( $x ) . ',' . $Y ( $y - $h / 2 ) . 'L' . $X ( $x + $w / 2 ) . ',' . $Y ( $y )
         . 'L' . $X ( $x ) . ',' . $Y ( $y + $h / 2 ) . 'L' . $X ( $x - $w / 2 ) . ',' . $Y ( $y ) . 'Z"/>';

  }

  function padDiagramDrawFork ( $node, $X, $Y ) {

    list ( $x, $y, $w, $h ) = [ $node ['x'], $node ['y'], $node ['w'], $node ['h'] ];

    return '<rect class="pd-start" x="' . $X ( $x - $w / 2 ) . '" y="' . $Y ( $y - $h / 2 ) . '" width="' . padChartXY ( $w ) . '" height="' . padChartXY ( $h ) . '" rx="2"/>';

  }

  // The rules of the state diagram's own shapes: the dots and bars in the colour of the
  // edges, the ring of the end on the background, the line under a name with a description.

  function padDiagramStateStyle () {

    return '<style' . padStyleNonce () . '>'
         . '.pad-chart-diagram .pd-start{fill:var(--pad-diagram-edge)}'
         . '.pad-chart-diagram .pd-stop{fill:var(--pad-chart-surface);stroke:var(--pad-diagram-edge);stroke-width:1.5}'
         . '.pad-chart-diagram .pd-line{stroke:var(--pad-diagram-border);stroke-width:1}'
         . '.pad-chart-diagram .pd-desc{font-size:11px}'
         . '</style>';

  }

?>
