<?php

  // The class diagram - diagrams/types/class.php: UML classes as boxes of three parts - the
  // name, the fields, the methods - and the relations between them, written as Mermaid's
  // classDiagram writes them and laid out in layers as a flowchart is (padDiagramGraph).
  //
  //   class Animal {                     a class and its members, a line each; a member
  //     <<abstract>>                     with ( is a method; + - # ~ its visibility, a $
  //     +String name                     at its end static (underlined), a * abstract
  //     +move(int metres) bool           (in italics); List~int~ is List<int>
  //   }
  //   Duck : +swim()                     a member on a line of its own
  //   <<interface>> Swimmer              the stereotype over the name
  //   Animal <|-- Duck                   inherits from (a hollow triangle at Animal)
  //   Swimmer <|.. Duck                  implements (dashed)
  //   Car *-- Engine                     is made of (a filled diamond at Car)
  //   Pond o-- Duck                      holds (a hollow diamond)
  //   Customer "1" --> "*" Order : buys  an association, its multiplicities and a label
  //   Order ..> Invoice                  depends on; -- and .. a plain link
  //
  // The class a triangle or a diamond stands at - the parent, the whole - is laid out above
  // the other, whichever side of the line it is written on.

  function padDiagramClass ( $source, $direction, $edgeForm, $title ) {

    global $padCheckSyntax;

    $name    = '[\p{L}\p{M}\p{N}_]+';
    $generic = '(?:~[^~]*~)?';
    $classes = $edges = $bad = [];
    $written = '';
    $first   = TRUE;
    $open    = NULL;

    $class = function ( $id ) use ( &$classes ) {
      $classes [$id] ??= [ 'text' => $id, 'note' => '', 'fields' => [], 'methods' => [] ];
      return $id;
    };

    $member = function ( $id, $text ) use ( &$classes ) {
      $text = trim ( $text );
      if ( preg_match ( '/^<<(.+)>>$/', $text, $m ) )
        $classes [$id] ['note'] = trim ( $m [1] );
      elseif ( $text !== '' )
        $classes [$id] [ str_contains ( $text, '(' ) ? 'methods' : 'fields' ] [] = $text;
    };

    $marks = [ '<|' => 'triangle', '|>' => 'triangle', '*' => 'diamond', 'o' => 'odiamond', '<' => 'open', '>' => 'open' ];

    foreach ( preg_split ( '/\r?\n/', (string) $source ) as $number => $line ) {

      $line = preg_match ( '/^\s*%%/', $line ) ? '' : trim ( $line );

      if ( $line === '' )
        continue;

      if ( $first and preg_match ( '/^classDiagram(-v2)?$/', $line ) ) {
        $first = FALSE;
        continue;
      }

      $first = FALSE;

      // Inside the braces of a class every line is a member, until the closing brace.

      if ( $open !== NULL ) {
        if ( $line == '}' )
          $open = NULL;
        else
          $member ( $open, $line );
        continue;
      }

      if ( preg_match ( '/^direction\s+(TB|TD|BT|LR|RL)$/i', $line, $m ) )
        $written = strtoupper ( $m [1] );
      elseif ( preg_match ( "/^class\s+($name)(~[^~]*~)?(?:\s*\[\s*\"([^\"]*)\"\s*\])?\s*(\{\s*\}?)?$/u", $line, $m ) ) {
        $class ( $m [1] );
        if ( ( $m [3] ?? '' ) !== '' )
          $classes [ $m [1] ] ['text'] = padDiagramFlat ( $m [3] );
        elseif ( ( $m [2] ?? '' ) !== '' )
          $classes [ $m [1] ] ['text'] = $m [1] . '<' . trim ( $m [2], '~' ) . '>';
        if ( trim ( $m [4] ?? '' ) == '{' )
          $open = $m [1];
      } elseif ( preg_match ( "/^<<(.+)>>\s*($name)$/u", $line, $m ) )
        $classes [ $class ( $m [2] ) ] ['note'] = trim ( $m [1] );
      elseif ( preg_match ( "/^($name)$generic\s*(?:\"([^\"]*)\")?\s*(<\||\*|o|<)?(--|\.\.)(\|>|\*|o|>)?\s*(?:\"([^\"]*)\")?\s*($name)$generic\s*(?::\s*(.*))?$/u", $line, $m ) ) {

        $left  = $marks [ $m [3] ] ?? '';
        $right = $marks [ $m [5] ] ?? '';
        $ends  = [ $m [2], $m [6] ];
        $pair  = [ $class ( $m [1] ), $class ( $m [7] ) ];

        // The parent and the whole go above: the mark written on the right is turned to the
        // tail, with the classes and their multiplicities.

        if ( $left === '' and in_array ( $right, [ 'triangle', 'diamond', 'odiamond' ] ) ) {
          $pair  = array_reverse ( $pair );
          $ends  = array_reverse ( $ends );
          $left  = $right;
          $right = '';
        }

        $edges [] = [ $pair [0], $pair [1], padDiagramFlat ( $m [8] ?? '' ), $m [4] == '..' ? 'dashed' : 'solid', FALSE,
                      'tail' => $left, 'head' => $right, 'ends' => $ends ];

      } elseif ( preg_match ( "/^($name)$generic\s*:\s*(.+)$/u", $line, $m ) )
        $member ( $class ( $m [1] ), $m [2] );
      else
        $bad [] = [ $number + 1, $line ];

    }

    if ( $bad and $padCheckSyntax )
      padError ( 'the class diagram cannot read line ' . $bad [0] [0] . ": '" . padMakeSafe ( $bad [0] [1], 60 ) . "' - write class A { +field }, A : +method(), A <|-- B, A *-- B, A o-- B, A --> B or A ..> B" );

    if ( $open !== NULL and $padCheckSyntax )
      padError ( "the class diagram has a class $open whose { no } closes" );

    if ( ! $classes )
      return '';

    if ( $direction === '' )
      $direction = $written !== '' ? $written : 'TB';

    if ( $direction == 'TD' )
      $direction = 'TB';

    $nodes = [];

    foreach ( $classes as $id => $one )
      $nodes [$id] = padDiagramClassSize ( $one );

    $desc = [];

    foreach ( $edges as $edge )
      $desc [] = padDiagramClassSay ( $edge, $nodes );

    // The members are said as they are shown, with what the $ and the * stand for.

    $say = function ( $member ) {
      list ( $text, $class ) = padDiagramClassMember ( $member );
      return $text . [ '' => '', 'pd-static' => ' (static)', 'pd-abstract' => ' (abstract)' ] [$class];
    };

    foreach ( $nodes as $node )
      $desc [] = $node ['text'] . ( $node ['note'] !== '' ? ' (' . $node ['note'] . ')' : '' )
               . ( $node ['fields'] || $node ['methods'] ? ': ' . implode ( ', ', array_map ( $say, array_merge ( $node ['fields'], $node ['methods'] ) ) ) : '' );

    return padDiagramGraph ( $nodes, $edges, $desc, $direction, $edgeForm, $title, padDiagramClassStyle (), [ 'gap' => 50, 'spread' => 20 ] );

  }

  // A relation in words: Duck extends Animal, Duck implements Swimmer, Car is made of Engine,
  // Pond holds Duck, Order depends on Invoice, and an arrow or a line for the rest, with the
  // multiplicities and the label.

  function padDiagramClassSay ( $edge, $nodes ) {

    list ( $a, $b, $label, $style ) = $edge;

    $one = fn ( $id, $end ) => $nodes [$id] ['text'] . ( $end !== '' ? " ($end)" : '' );
    $A   = $one ( $a, $edge ['ends'] [0] );
    $B   = $one ( $b, $edge ['ends'] [1] );

    if ( $edge ['tail'] == 'triangle' and $edge ['head'] === '' )
      $say = "$B " . ( $style == 'dashed' ? 'implements' : 'extends' ) . " $A";
    elseif ( $edge ['tail'] == 'diamond' and $edge ['head'] === '' )
      $say = "$A is made of $B";
    elseif ( $edge ['tail'] == 'odiamond' and $edge ['head'] === '' )
      $say = "$A holds $B";
    elseif ( $edge ['head'] == 'open' and $edge ['tail'] === '' and $style == 'dashed' )
      $say = "$A depends on $B";
    elseif ( $edge ['head'] == 'triangle' and $edge ['tail'] === '' )
      $say = "$A " . ( $style == 'dashed' ? 'implements' : 'extends' ) . " $B";
    elseif ( $edge ['head'] !== '' and $edge ['tail'] !== '' )
      $say = "$A ↔ $B";
    elseif ( $edge ['head'] !== '' )
      $say = "$A → $B";
    elseif ( $edge ['tail'] !== '' )
      $say = "$A ← $B";
    else
      $say = "$A — $B";

    return $say . ( $label !== '' ? " ($label)" : '' );

  }

  // A member as it is shown: List~int~ as List<int>, without the $ or * that marks it static
  // or abstract - at its end, or right after the ( ) of a method with its type behind:
  // count()$ int - [ text, class ].

  function padDiagramClassMember ( $text ) {

    $class = '';

    if ( preg_match ( '/^(.*\))\s*([$*])(.*)$/', $text, $m ) or preg_match ( '/^(.+?)\s*([$*])()$/', $text, $m ) ) {
      $text  = $m [1] . $m [3];
      $class = ( $m [2] == '$' ) ? 'pd-static' : 'pd-abstract';
    }

    return [ preg_replace ( '/~([^~]*)~/', '<$1>', $text ), $class ];

  }

  // The size of a class: the stereotype and the name in the head, a line of 16 for every
  // member under it; the fields and the methods each under a line of their own when it has
  // any member at all, as UML draws the parts even when one is empty.

  function padDiagramClassSize ( $class ) {

    $members = array_merge ( $class ['fields'], $class ['methods'] );
    $width   = padDiagramWidth ( $class ['text'] ) + 32;

    if ( $class ['note'] !== '' )
      $width = max ( $width, padDiagramWidth ( '«' . $class ['note'] . '»' ) + 24 );

    foreach ( $members as $member )
      $width = max ( $width, padDiagramWidth ( padDiagramClassMember ( $member ) [0] ) + 20 );

    $head = ( $class ['note'] !== '' ? 46 : 32 );
    $body = $members ? ( max ( 1, count ( $class ['fields'] ) ) + max ( 1, count ( $class ['methods'] ) ) ) * 16 + 20 : 0;

    return $class + [ 'shape' => 'class', 'lines' => [], 'head' => $head, 'w' => max ( 90, $width ), 'h' => $head + $body ];

  }

  function padDiagramDrawClass ( $node, $X, $Y ) {

    list ( $x, $y, $w, $h ) = [ $node ['x'], $node ['y'], $node ['w'], $node ['h'] ];

    $top  = $y - $h / 2;
    $left = $x - $w / 2;
    $svg  = '<g><title>' . padChartAttr ( $node ['text'] ) . '</title>'
          . '<rect class="pd-node" x="' . $X ( $left ) . '" y="' . $Y ( $top ) . '" width="' . padChartXY ( $w ) . '" height="' . padChartXY ( $h ) . '" rx="3"/>';

    if ( $node ['note'] !== '' )
      $svg .= '<text class="pd-type" x="' . $X ( $x ) . '" y="' . $Y ( $top + 17 ) . '" text-anchor="middle">«' . padChartAttr ( $node ['note'] ) . '»</text>';

    $svg .= '<text class="pd-name" x="' . $X ( $x ) . '" y="' . $Y ( $top + $node ['head'] - 12 ) . '" text-anchor="middle">' . padChartAttr ( $node ['text'] ) . '</text>';

    if ( ! $node ['fields'] and ! $node ['methods'] )
      return "$svg</g>";

    $at = $top + $node ['head'];

    foreach ( [ $node ['fields'], $node ['methods'] ] as $part ) {

      $svg .= '<path class="pd-line" d="M' . $X ( $left ) . ',' . $Y ( $at ) . 'H' . $X ( $left + $w ) . '"/>';
      $at  += 5;

      foreach ( $part as $member ) {
        list ( $text, $class ) = padDiagramClassMember ( $member );
        $svg .= '<text' . ( $class !== '' ? " class=\"$class\"" : '' ) . ' x="' . $X ( $left + 10 ) . '" y="' . $Y ( $at + 12 ) . '">' . padChartAttr ( $text ) . '</text>';
        $at  += 16;
      }

      $at += ( $part ? 0 : 16 ) + 5;

    }

    return "$svg</g>";

  }

  // The rules of the class boxes - the name in bold, the stereotype and the abstract members
  // in italics, the static ones underlined, a line between the parts - and of the marks.

  function padDiagramClassStyle () {

    return '<style' . padStyleNonce () . '>'
         . '.pad-chart-diagram .pd-name{font-weight:700}'
         . '.pad-chart-diagram .pd-type{font-size:11px;font-style:italic}'
         . '.pad-chart-diagram .pd-abstract{font-style:italic}'
         . '.pad-chart-diagram .pd-static{text-decoration:underline}'
         . '.pad-chart-diagram .pd-line{stroke:var(--pad-diagram-border);stroke-width:1}'
         . padDiagramMarkStyle ()
         . '</style>';

  }

?>
