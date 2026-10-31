<?php

  // The entity-relationship diagram - diagrams/types/er.php: entities as tables of their
  // attributes and the relations between them with the feet of a crow for how many, written
  // as Mermaid's erDiagram writes them and laid out in layers as a flowchart is
  // (padDiagramGraph).
  //
  //   CUSTOMER ||--o{ ORDER : places     a relation and its label: on each side how many of
  //   ORDER ||--|{ LINE-ITEM : contains  that side's entity go with one of the other -
  //   CUSTOMER }|..|{ ADDRESS : uses     || exactly one, |o (o| on the right) zero or one,
  //                                      }| (|{) one or more, }o (o{) zero or more; -- a
  //                                      relation that identifies, .. one that does not
  //   CUSTOMER {                         the attributes, a line each: the type, the name,
  //     string name                      PK, FK or UK and a "comment"
  //     int id PK "the number"
  //   }

  function padDiagramEr ( $source, $direction, $edgeForm, $title ) {

    global $padCheckSyntax;

    $name     = '[\p{L}\p{M}\p{N}_](?:[\p{L}\p{M}\p{N}_-]*[\p{L}\p{M}\p{N}_])?';
    $entities = $edges = $bad = [];
    $written  = '';
    $first    = TRUE;
    $open     = NULL;

    $entity = function ( $id ) use ( &$entities ) {
      $entities [$id] ??= [ 'text' => $id, 'rows' => [] ];
      return $id;
    };

    $left  = [ '||' => 'one', '|o' => 'zeroone', '}|' => 'many', '}o' => 'zeromany' ];
    $right = [ '||' => 'one', 'o|' => 'zeroone', '|{' => 'many', 'o{' => 'zeromany' ];

    foreach ( preg_split ( '/\r?\n/', (string) $source ) as $number => $line ) {

      $line = preg_match ( '/^\s*%%/', $line ) ? '' : trim ( $line );

      if ( $line === '' )
        continue;

      if ( $first and $line == 'erDiagram' ) {
        $first = FALSE;
        continue;
      }

      $first = FALSE;

      // Inside the braces of an entity every line is an attribute, until the closing brace.

      if ( $open !== NULL ) {
        if ( $line == '}' )
          $open = NULL;
        elseif ( preg_match ( '/^([^\s"]+)\s+([^\s"]+)((?:\s+(?:PK|FK|UK)(?:\s*,\s*(?:PK|FK|UK))*)?)\s*(?:"([^"]*)")?$/u', $line, $m ) )
          $entities [$open] ['rows'] [] = [ $m [1], $m [2], preg_replace ( '/\s*,\s*/', ', ', trim ( $m [3] ) ), $m [4] ?? '' ];
        else
          $bad [] = [ $number + 1, $line ];
        continue;
      }

      if ( preg_match ( '/^direction\s+(TB|TD|BT|LR|RL)$/i', $line, $m ) )
        $written = strtoupper ( $m [1] );
      elseif ( preg_match ( "/^($name)\s*(\|\||\|o|\}\||\}o)(--|\.\.)(\|\||o\||\|\{|o\{)\s*($name)\s*(?::\s*(.*))?$/u", $line, $m ) )
        $edges [] = [ $entity ( $m [1] ), $entity ( $m [5] ), padDiagramFlat ( trim ( $m [6] ?? '', " \"" ) ),
                      $m [3] == '..' ? 'dashed' : 'solid', FALSE, 'tail' => $left [ $m [2] ], 'head' => $right [ $m [4] ] ];
      elseif ( preg_match ( "/^($name)(?:\s*\[\s*\"([^\"]*)\"\s*\])?\s*(\{\s*\}?)?$/u", $line, $m ) ) {
        $entity ( $m [1] );
        if ( ( $m [2] ?? '' ) !== '' )
          $entities [ $m [1] ] ['text'] = padDiagramFlat ( $m [2] );
        if ( trim ( $m [3] ?? '' ) == '{' )
          $open = $m [1];
      } else
        $bad [] = [ $number + 1, $line ];

    }

    if ( $bad and $padCheckSyntax )
      padError ( 'the entity-relationship diagram cannot read line ' . $bad [0] [0] . ": '" . padMakeSafe ( $bad [0] [1], 60 )
               . "' - write A ||--o{ B : label, A }|..|{ B, A { type name PK \"comment\" }" );

    if ( $open !== NULL and $padCheckSyntax )
      padError ( "the entity-relationship diagram has an entity $open whose { no } closes" );

    if ( ! $entities )
      return '';

    if ( $direction === '' )
      $direction = $written !== '' ? $written : 'TB';

    if ( $direction == 'TD' )
      $direction = 'TB';

    $nodes = [];

    foreach ( $entities as $id => $one )
      $nodes [$id] = padDiagramErSize ( $one );

    $how  = [ 'one' => 'exactly one', 'zeroone' => 'zero or one', 'many' => 'one or more', 'zeromany' => 'zero or more' ];
    $desc = [];

    foreach ( $edges as $edge )
      $desc [] = $nodes [ $edge [0] ] ['text'] . ' ' . ( $edge [2] !== '' ? $edge [2] : 'relates to' ) . ' ' . $nodes [ $edge [1] ] ['text']
               . ': ' . $how [ $edge ['tail'] ] . ' ' . $nodes [ $edge [0] ] ['text'] . ' to ' . $how [ $edge ['head'] ] . ' ' . $nodes [ $edge [1] ] ['text'];

    foreach ( $nodes as $node )
      if ( $node ['rows'] )
        $desc [] = $node ['text'] . ': ' . implode ( ', ', array_map ( fn ( $row ) => $row [1] . ' (' . implode ( ', ', array_filter ( [ $row [0], $row [2], $row [3] ], 'strlen' ) ) . ')', $node ['rows'] ) );
      else
        $desc [] = $node ['text'];

    return padDiagramGraph ( $nodes, $edges, $desc, $direction, $edgeForm, $title, padDiagramErStyle (), [ 'gap' => 80, 'spread' => 30 ] );

  }

  // The size of an entity: its name in the head, a row of 20 for every attribute under it,
  // the columns - type, name, keys, comment - as wide as their widest text.

  function padDiagramErSize ( $entity ) {

    $cols = [ 0, 0, 0, 0 ];

    foreach ( $entity ['rows'] as $row )
      foreach ( $row as $k => $text )
        $cols [$k] = max ( $cols [$k], padDiagramWidth ( $text ) );

    $inside = 0;
    foreach ( $cols as $width )
      $inside += $width ? $width + 14 : 0;

    return $entity + [ 'shape' => 'entity', 'lines' => [], 'cols' => $cols,
                       'w' => max ( 90, padDiagramWidth ( $entity ['text'] ) + 32, $inside + 10 ),
                       'h' => 30 + 20 * count ( $entity ['rows'] ) + ( $entity ['rows'] ? 4 : 0 ) ];

  }

  function padDiagramDrawEntity ( $node, $X, $Y ) {

    list ( $x, $y, $w, $h ) = [ $node ['x'], $node ['y'], $node ['w'], $node ['h'] ];

    $top   = $y - $h / 2;
    $left  = $x - $w / 2;
    $box   = 'x="' . $X ( $left ) . '" y="' . $Y ( $top ) . '" width="' . padChartXY ( $w ) . '" height="' . padChartXY ( $h ) . '" rx="3"';
    $svg   = '<g><title>' . padChartAttr ( $node ['text'] ) . "</title><rect class=\"pd-body\" $box/>"
           . '<rect class="pd-headfill" x="' . $X ( $left ) . '" y="' . $Y ( $top ) . '" width="' . padChartXY ( $w ) . '" height="30" rx="3"/>'
           . '<text class="pd-name" x="' . $X ( $x ) . '" y="' . $Y ( $top + 19 ) . '" text-anchor="middle">' . padChartAttr ( $node ['text'] ) . '</text>';

    $at = $top + 30;

    foreach ( $node ['rows'] as $i => $row ) {

      if ( $i )
        $svg .= '<path class="pd-row" d="M' . $X ( $left ) . ',' . $Y ( $at + 2 + 20 * $i ) . 'H' . $X ( $left + $w ) . '"/>';

      $col = $left + 10;

      foreach ( $row as $k => $text ) {
        if ( $text !== '' )
          $svg .= '<text class="' . [ 'pd-muted', 'pd-attr', 'pd-key', 'pd-muted pd-note-text' ] [$k] . '" x="' . $X ( $col ) . '" y="' . $Y ( $at + 16 + 20 * $i ) . '">' . padChartAttr ( $text ) . '</text>';
        $col += $node ['cols'] [$k] ? $node ['cols'] [$k] + 14 : 0;
      }

    }

    return $svg . '<path class="pd-line" d="M' . $X ( $left ) . ',' . $Y ( $top + 30 ) . 'H' . $X ( $left + $w ) . "\"/><rect class=\"pd-outline\" $box/></g>";

  }

  // The rules of the entity tables - the head in the colour of a node, the rows on the
  // background - and of the feet.

  function padDiagramErStyle () {

    return '<style' . padStyleNonce () . '>'
         . '.pad-chart-diagram .pd-body{fill:var(--pad-chart-surface)}'
         . '.pad-chart-diagram .pd-headfill{fill:var(--pad-diagram-node)}'
         . '.pad-chart-diagram .pd-outline{fill:none;stroke:var(--pad-diagram-border);stroke-width:1.5}'
         . '.pad-chart-diagram .pd-line{stroke:var(--pad-diagram-border);stroke-width:1}'
         . '.pad-chart-diagram .pd-row{stroke:var(--pad-chart-grid);stroke-width:1}'
         . '.pad-chart-diagram .pd-name{font-weight:700}'
         . '.pad-chart-diagram .pd-muted{opacity:.7}'
         . '.pad-chart-diagram .pd-note-text{font-style:italic}'
         . '.pad-chart-diagram .pd-key{font-size:10px;font-weight:700;fill:var(--pad-diagram-border)}'
         . padDiagramMarkStyle ()
         . '</style>';

  }

?>
