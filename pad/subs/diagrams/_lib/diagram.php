<?php

  // The diagram subsystem: diagrams from a few lines of text, in the Mermaid way of writing
  // them, laid out on the server and written as inline SVG - no JavaScript, so a diagram
  // works in print and in an e-mail, and the same text always draws the same picture.
  //
  //   {diagram}                          {diagram type='sequence'}
  //     A[Order placed] --> B{Paid?}       Shop ->> Bank: Charge 20 EUR
  //     B -->|yes| C((Ship))               Bank -->> Shop: Approved
  //     B -.->|no| D[Remind]             {/diagram}
  //   {/diagram}
  //
  // A diagram kind is a file of subs/diagrams/types/: the kinds there are are the kinds a diagram
  // has (padDiagramTypes), the {diagram} tag names one by its type=, the diagram: type by its
  // name - {diagram:flowchart}, or bare, {flowchart}, when nothing else has the name - and
  // subs/diagrams/diagram.php is the body all of them run. A kind's file reads its options and
  // answers the SVG, drawn by the functions of this directory: this file the parts every
  // kind shares and the flowchart, the files beside it the other kinds.
  //
  // padDiagramTag        a diagram of one kind: its file in subs/diagrams/types/ draws it
  // padDiagramParse      the lines of a flowchart: nodes, their shapes and the edges
  // padDiagramLayout     the layers: ranks by the longest path, the order in each rank by
  //                      barycentres, the positions across by isotonic regression
  // padDiagramFlow       the SVG of a flowchart
  // padDiagramSequence   the SVG of a sequence diagram: lifelines and the messages between
  //                      (sequence.php)
  //
  // The picture opens as a {chart} does (padChartOpen): role="img", a <title> and a <desc>
  // that says every edge or message in words. The colours are custom properties on
  // .pad-chart-diagram - --pad-diagram-node, --pad-diagram-border, --pad-diagram-decision,
  // --pad-diagram-decision-border, --pad-diagram-round, --pad-diagram-round-border,
  // --pad-diagram-edge and --pad-diagram-note - with light-dark() defaults.

  // The SVG opening and its coordinates are {chart}'s, subs/charts/_lib/chart.php.

  include_once PAD . 'subs/charts/_lib/chart.php';

  // The kinds of diagram: the files of subs/diagrams/types/ (padKinds, lib/subsystem.php) - a kind
  // is added by adding its file.

  function padDiagramTypes () {

    return padKinds ( 'diagrams' );

  }

  function padDiagramType ( $name ) {

    return padKind ( 'diagrams', $name );

  }

  // A diagram of one kind: its file in subs/diagrams/types/ draws it. The file runs in this
  // function, with the kind and the text at hand to padDiagramSource in $padDiagramNow - put
  // back afterwards, for a diagram drawn while another one is. A kind reads only the options
  // it draws with, so an option of another kind - direction= on a sequence diagram - is one
  // that nothing reads, which the strict check reports.

  function padDiagramTag ( $kind, $text ) {

    global $padDiagramNow;

    $before        = $padDiagramNow ?? NULL;
    $padDiagramNow = [ 'kind' => $kind, 'text' => (string) $text ];

    $svg = include PAD . "subs/diagrams/types/$kind.php";

    $padDiagramNow = $before;

    return $svg;

  }

  // The lines of the diagram drawn now, their shared indent taken off by the body.

  function padDiagramSource () {

    return $GLOBALS ['padDiagramNow'] ['text'] ?? '';

  }

  // The kind a first line names the Mermaid way, or ''. The word is the kind's name, with
  // Diagram after it - sequenceDiagram, stateDiagram-v2, classDiagram, erDiagram - or Graph,
  // gitGraph, or the name alone - mindmap, journey - and the kinds are the files there are:
  // no list of Mermaid's words is kept. A name with Diagram or Graph names the kind whatever
  // the tag says, as a first line sequenceDiagram did: the lines are written for that kind,
  // and no other can read them. A name alone is the kind only where the tag names none -
  // {diagram} without type= - since it could be the first line of the kind the tag does
  // name: the root of a tree called journey. The line stands alone, a direction after a
  // name with Graph aside (gitGraph LR:), so a flowchart whose first line is a node called
  // mindmap, and an edge from it, stays one.

  function padDiagramHeader ( $text, $named ) {

    if ( ! preg_match ( '/^\s*([a-z]+)(?:((?:Diagram|Graph)(?:-v2)?)(?:[ \t]+[A-Z]{2}:?)?)?[ \t]*(\r?\n|$)/', (string) $text, $match ) )
      return '';

    $more = $match [2] ?? '';

    if ( $more === '' and $named )
      return '';

    $kind = $match [1] . ( str_starts_with ( $more, 'Graph' ) ? 'graph' : '' );

    return padDiagramType ( $kind ) ? $kind : '';

  }

  // direction= of a kind that is laid out in layers: TB (the default, TD the same), BT, LR or
  // RL, or '' when the tag gives none and the lines may name one.

  function padDiagramDirection () {

    global $padCheckSyntax;

    $direction = strtoupper ( trim ( (string) padTagParm ( 'direction', '' ) ) );

    if ( ! in_array ( $direction, [ '', 'TB', 'TD', 'BT', 'LR', 'RL' ] ) ) {
      if ( $padCheckSyntax )
        padError ( "the diagram has no direction '" . padMakeSafe ( $direction, 20 ) . "' - TB, BT, LR or RL" );
      $direction = '';
    }

    return $direction;

  }

  // edges= of a kind that is laid out in layers: orthogonal (the default), straight or curved.

  function padDiagramEdges () {

    global $padCheckSyntax;

    $form = strtolower ( trim ( (string) padTagParm ( 'edges', 'orthogonal' ) ) );

    if ( ! in_array ( $form, [ 'orthogonal', 'straight', 'curved' ] ) ) {
      if ( $padCheckSyntax )
        padError ( "the diagram has no edges '" . padMakeSafe ( $form, 20 ) . "' - orthogonal, straight or curved" );
      $form = 'orthogonal';
    }

    return $form;

  }

  // The node shapes by their brackets, the edges by their arrows.

  function padDiagramShapes () {

    return [ '((' => [ '))', 'circle' ], '([' => [ '])', 'round'    ], '[(' => [ ')]', 'database' ],
             '{{' => [ '}}', 'hexagon' ], '['  => [ ']',  'box'      ], '('  => [ ')',  'round'    ],
             '{'  => [ '}',  'diamond' ] ];

  }

  // The lines of a flowchart as [ direction, nodes, edges ]. A node is [ text, shape ] by
  // its id, in the order the text first names it; an edge is [ from, to, label, style,
  // arrow ]. A line is a chain - A --> B --> C - and & joins nodes on one side: A & B --> C.
  // A line that is no chain comes back in $bad, with its number; a ; may end a line.

  function padDiagramParse ( $source, &$bad ) {

    $direction = '';
    $nodes     = [];
    $edges     = [];
    $bad       = [];

    $edge = '/\G\s*(?:(?:--|==|-\.)\s+(?<text>[^|]+?)\s+(?<long>-->|==>|\.->|---|===|\.-)'
          . '|(?<arrow>-{2,}>|={2,}>|-\.+->|-{3,}|={3,}|-\.+-))\s*(?:\|(?<label>[^|]*)\|)?/u';

    foreach ( preg_split ( '/\r?\n/', $source ) as $number => $line ) {

      // A comment is a line of its own that starts with %%, as Mermaid has it: a %% anywhere
      // cut the line there, and a node "Save 10%%" could not be read.

      $line = preg_match ( '/^\s*%%/', $line ) ? '' : rtrim ( trim ( $line ), ';' );

      if ( $line === '' )
        continue;

      if ( preg_match ( '/^(graph|flowchart)(\s+(TB|TD|BT|LR|RL))?$/i', $line, $match ) ) {
        $direction = strtoupper ( $match [3] ?? '' );
        continue;
      }

      $at    = 0;
      $from  = padDiagramNodes ( $line, $at, $nodes );
      $chain = TRUE;

      if ( $from === NULL )
        $chain = FALSE;

      while ( $chain and $at < strlen ( $line ) and preg_match ( $edge, $line, $match, 0, $at ) ) {

        $at  += strlen ( $match [0] );
        $kind = ( $match ['arrow'] ?? '' ) !== '' ? $match ['arrow'] : $match ['long'];
        $to   = padDiagramNodes ( $line, $at, $nodes );

        if ( $to === NULL ) {
          $chain = FALSE;
          break;
        }

        $label = padDiagramFlat ( trim ( ( $match ['label'] ?? '' ) !== '' ? $match ['label'] : ( $match ['text'] ?? '' ), " \"" ) );
        $style = str_contains ( $kind, '.' ) ? 'dashed' : ( str_contains ( $kind, '=' ) ? 'thick' : 'solid' );
        $arrow = str_ends_with ( $kind, '>' );

        foreach ( $from as $a )
          foreach ( $to as $b )
            $edges [] = [ $a, $b, $label, $style, $arrow ];

        $from = $to;

      }

      if ( ! $chain or trim ( substr ( $line, $at ) ) !== '' )
        $bad [] = [ $number + 1, $line ];

    }

    return [ $direction, $nodes, $edges ];

  }

  // The nodes at $at: one or more joined by &, each an id with its shape - A, A[Text],
  // B{Ask?}, C((Done)), D(Soft), E([Stadium]), F[(Database)], G{{Prepare}}. A quoted text
  // keeps its brackets: A["[x]"].
  // The first mention with a shape names it; a later one keeps that.

  function padDiagramNodes ( $line, &$at, &$nodes ) {

    $ids = [];

    while ( TRUE ) {

      if ( ! preg_match ( '/\G\s*([\p{L}\p{M}\p{N}_]+)/u', $line, $match, 0, $at ) )
        return NULL;

      $at   += strlen ( $match [0] );
      $id    = $match [1];
      $text  = NULL;
      $shape = 'box';

      foreach ( padDiagramShapes () as $open => list ( $close, $kind ) )
        if ( substr ( $line, $at, strlen ( $open ) ) === $open ) {

          $start = $at + strlen ( $open );

          if ( preg_match ( '/\G\s*"([^"]*)"\s*/u', $line, $quoted, 0, $start ) and substr ( $line, $start + strlen ( $quoted [0] ), strlen ( $close ) ) === $close ) {
            $text = $quoted [1];
            $end  = $start + strlen ( $quoted [0] );
          } elseif ( ( $end = strpos ( $line, $close, $start ) ) !== FALSE )
            $text = trim ( substr ( $line, $start, $end - $start ) );
          else
            return NULL;

          $at    = $end + strlen ( $close );
          $shape = $kind;
          break;

        }

      if ( ! isset ( $nodes [$id] ) or ( $text !== NULL and $nodes [$id] [2] ) )
        $nodes [$id] = [ $text ?? $id, $shape, $text === NULL ];

      $ids [] = $id;

      if ( ! preg_match ( '/\G\s*&/', $line, $match, 0, $at ) )
        return $ids;

      $at += strlen ( $match [0] );

    }

  }

  // Where the k-th loop of a node on itself starts - [ x, r ]: x on the node's side, r how
  // far the loop reaches from it, negative on the left ($side -1). On a diamond it starts
  // on the outline, 8 above the middle: a quarter of the width in from the tip, a decision
  // of more than a few words had the loop and its label inside it, hidden by its fill.

  function padDiagramLoopAt ( $n, $k, $side ) {

    $x = $n ['x'] + $side * ( $n ['shape'] == 'diamond' ? $n ['w'] / 2 * max ( 0, 1 - 16 / max ( 1, $n ['h'] ) ) : $n ['w'] / 2 );

    return [ $x, $side * ( 34 + 22 * $k ) ];

  }

  // A text on one line - an edge's label, a message, a participant's name, a description:
  // its <br> a space, where it was printed as &lt;br&gt;; a node and a note break there.

  function padDiagramFlat ( $text ) {

    return trim ( preg_replace ( '/\s*<br\s*\/?>\s*/i', ' ', (string) $text ) );

  }

  // A word of wide characters that does not fit on a line is broken between them, as those
  // scripts break - it has no spaces, and stood on one line far wider than its box.

  function padDiagramPieces ( $word, $max ) {

    if ( mb_strwidth ( $word ) <= $max or mb_strwidth ( $word ) == mb_strlen ( $word ) )
      return [ $word ];

    $pieces = [ '' ];

    foreach ( mb_str_split ( $word ) as $char )
      if ( end ( $pieces ) !== '' and mb_strwidth ( end ( $pieces ) . $char ) > $max )
        $pieces [] = $char;
      else
        $pieces [ count ( $pieces ) - 1 ] .= $char;

    return $pieces;

  }

  // A text in lines of at most $max characters, broken at spaces; <br> breaks it too.

  function padDiagramWrap ( $text, $max = 24 ) {

    $lines = [];

    // <br> alone, as it says and as Mermaid has it: a backslash and an n broke too, and
    // C:\new\backup was drawn as "C:" over "ew\backup", with no way to write it.

    foreach ( preg_split ( '/<br\s*\/?>/i', (string) $text ) as $part ) {

      $line = '';

      foreach ( preg_split ( '/\s+/u', trim ( $part ) ) as $word )
        foreach ( padDiagramPieces ( $word, $max ) as $k => $piece )
          if ( $line === '' )
            $line = $piece;
          elseif ( ! $k and mb_strwidth ( "$line $piece" ) <= $max )
            $line .= " $piece";
          else {
            $lines [] = $line;
            $line     = $piece;
          }

      $lines [] = $line;

    }

    return $lines;

  }

  // The width of a text at the diagram's 12px, about.

  // A wide character - Chinese, Japanese, Korean - takes the room of two: thirteen of them
  // ran some twenty pixels past both sides of their box.

  function padDiagramWidth ( $text ) {

    return mb_strwidth ( (string) $text ) * 7;

  }

  // Lines of text centred on x, y: a <text> with a <tspan> per line.

  function padDiagramText ( $lines, $x, $y, $class = '' ) {

    $first = $y - ( count ( $lines ) - 1 ) * 8 + 4;
    $svg   = '<text' . ( $class !== '' ? " class=\"$class\"" : '' ) . ' x="' . padChartXY ( $x ) . '" y="' . padChartXY ( $first ) . '" text-anchor="middle">';

    foreach ( $lines as $i => $line )
      $svg .= '<tspan x="' . padChartXY ( $x ) . '"' . ( $i ? ' dy="16"' : '' ) . '>' . padChartAttr ( $line ) . '</tspan>';

    return "$svg</text>";

  }

  // The colours, as {chart} writes its own: the light steps, then light-dark() where the
  // browser has it.

  function padDiagramStyle () {

    $roles = [ 'node'            => [ '#eaf2fc', '#1b2a3d' ], 'border'          => [ '#2a78d6', '#5598e7' ],
               'decision'        => [ '#fdf3dc', '#3a2f14' ], 'decision-border' => [ '#c98500', '#eda100' ],
               'round'           => [ '#e1f5ec', '#15332a' ], 'round-border'    => [ '#1baf7a', '#1baf7a' ],
               'edge'            => [ '#6b6a66', '#a3a29a' ], 'note'            => [ '#fff8c5', '#3d3a1c' ] ];

    $light = $both = '';

    foreach ( $roles as $role => list ( $day, $night ) ) {
      $light .= "--pad-diagram-$role:$day;";
      $both  .= "--pad-diagram-$role:light-dark($day,$night);";
    }

    return '<style' . padStyleNonce () . '>'
         . ":where(.pad-chart-diagram){{$light}}"
         . "@supports (color:light-dark(#000,#fff)){:where(.pad-chart-diagram){{$both}}}"
         . '.pad-chart-diagram text{font-size:12px}'
         . '.pad-chart-diagram .pd-node{fill:var(--pad-diagram-node);stroke:var(--pad-diagram-border);stroke-width:1.5}'
         . '.pad-chart-diagram .pd-diamond{fill:var(--pad-diagram-decision);stroke:var(--pad-diagram-decision-border)}'
         . '.pad-chart-diagram .pd-circle{fill:var(--pad-diagram-round);stroke:var(--pad-diagram-round-border)}'
         . '.pad-chart-diagram .pd-edge{fill:none;stroke:var(--pad-diagram-edge);stroke-width:1.5;stroke-linejoin:round}'
         . '.pad-chart-diagram .pd-dashed{stroke-dasharray:5 4}'
         . '.pad-chart-diagram .pd-thick{stroke-width:3}'
         . '.pad-chart-diagram .pd-head{fill:var(--pad-diagram-edge)}'
         . '.pad-chart-diagram .pd-plate{fill:var(--pad-chart-surface)}'
         . '.pad-chart-diagram .pd-life{stroke:var(--pad-diagram-edge);stroke-width:1;stroke-dasharray:4 4}'
         . '.pad-chart-diagram .pd-note{fill:var(--pad-diagram-note);stroke:var(--pad-diagram-decision-border);stroke-width:1}'
         . '.pad-chart-diagram .pd-frame{fill:none;stroke:var(--pad-diagram-edge);stroke-width:1}'
         . '.pad-chart-diagram .pd-tab{fill:var(--pad-diagram-node);stroke:var(--pad-diagram-edge);stroke-width:1}'
         . '.pad-chart-diagram .pd-small{font-size:11px;font-weight:600}'
         . '.pad-chart-diagram .pd-number{fill:var(--pad-diagram-border)}'
         . '.pad-chart-diagram .pd-number-text{fill:var(--pad-chart-surface);font-size:10px;font-weight:700}'
         . '</style>';

  }

  // The size of a node from its text: a box or a stadium around the lines, a circle round
  // them, a diamond wide enough that the lines fit inside its slopes.

  function padDiagramSize ( $lines, $shape ) {

    $textW = 0;
    foreach ( $lines as $line )
      $textW = max ( $textW, padDiagramWidth ( $line ) );

    $textH = 16 * count ( $lines );

    if ( $shape == 'circle' ) {
      $d = max ( 44, hypot ( $textW, $textH ) + 16 );
      return [ $d, $d ];
    }

    if ( $shape == 'diamond' ) {
      $w = $textW * 1.8 + 40;
      return [ $w, max ( 44, ( $textH + 6 ) / ( 1 - ( $textW + 8 ) / $w ) ) ];
    }

    if ( $shape == 'database' )
      return [ max ( 60, $textW + 28 ), max ( 34, $textH + 16 ) + 12 ];

    return [ max ( 60, $textW + ( $shape == 'round' ? 36 : ( $shape == 'hexagon' ? 44 : 28 ) ) ), max ( 34, $textH + 16 ) ];

  }

  // How far the outline of a node reaches along the main axis - down in TB, right in LR -
  // at a distance $off across it from its middle: where an edge leaves or enters.

  function padDiagramReach ( $node, $off, $vertical ) {

    list ( $w, $h, $shape ) = [ $node ['w'], $node ['h'], $node ['shape'] ];

    $main  = $vertical ? $h / 2 : $w / 2;
    $cross = $vertical ? $w / 2 : $h / 2;
    $off   = min ( abs ( $off ), $cross * 0.8 );

    if ( $shape == 'dummy' )
      return 0;

    // The start and the end of a state diagram are round, its choice a diamond.

    if ( $shape == 'circle' or $shape == 'start' or $shape == 'end' )
      return sqrt ( max ( 0, $main * $main - $off * $off ) );

    if ( $shape == 'diamond' or $shape == 'choice' )
      return $main * ( 1 - $off / $cross );

    // A hexagon is pointed left and right: across a horizontal flow an edge ends on its
    // slanted sides, where the outline of a box left the arrowheads short of them.

    if ( $shape == 'hexagon' and ! $vertical )
      return $main - ( $h / 3 ) * ( $off / $cross );

    $r  = ( $shape == 'round' ) ? min ( $w, $h ) / 2 : 4;
    $in = max ( 0, $off - ( $cross - $r ) );

    return ( $main - $r ) + sqrt ( max ( 0, $r * $r - $in * $in ) );

  }

  // The layered layout. Cycles are broken by turning the edges that close one (found by a
  // depth-first walk in the order of the text); a node's rank is the longest path to it;
  // an edge over more than one rank passes a dummy node in each rank between, so it can
  // bend round the nodes there. The order in each rank comes from sweeps that sort by the
  // mean position of the neighbours - the barycentre - keeping the order with the fewest
  // crossings; the position across a rank is the one nearest the neighbours' mean that
  // keeps the nodes apart: isotonic regression (pool adjacent violators), so the result is
  // the same for the same text, every time. $ranking is padDiagramRanking's answer.

  function padDiagramLayout ( &$nodes, $edges, $vertical, $gaps, $ranking ) {

    list ( $rank, $turned, $dag ) = $ranking;

    // The chains: every edge as the nodes it passes, a dummy in each rank it crosses.

    $chains = [];
    $dummy  = 0;

    foreach ( $dag as $e => list ( $a, $b ) ) {

      $chain = [ $a ];

      for ( $r = $rank [$a] + 1; $r < $rank [$b]; $r++ ) {
        $id            = "\0" . $dummy++;
        $nodes [$id]   = [ 'shape' => 'dummy', 'w' => 8, 'h' => 0, 'lines' => [] ];
        $rank  [$id]   = $r;
        $chain []      = $id;
      }

      $chain []     = $b;
      $chains [$e]  = $chain;

      // An edge with a label across ranks carries it at the dummy in its middle, as large as
      // the label, so its rank makes room for it: half way along the path the label was put
      // beside the node of that rank, which covered it.

      $label = (string) ( $edges [$e] [2] ?? '' );

      if ( $label !== '' and count ( $chain ) > 2 ) {
        $mid                      = $chain [ intdiv ( count ( $chain ) - 1, 2 ) ];
        $nodes [$mid] ['w']       = padDiagramWidth ( $label ) + 8;
        $nodes [$mid] ['h']       = 18;
        $nodes [$mid] ['carries'] = $e;
      }

    }

    $ranks = $up = $down = [];

    foreach ( $rank as $id => $r )
      $ranks [$r] [] = $id;

    ksort ( $ranks );

    foreach ( $chains as $chain )
      for ( $i = 1; $i < count ( $chain ); $i++ ) {
        $down [ $chain [$i - 1] ] [] = $chain [$i];
        $up   [ $chain [$i] ]     [] = $chain [$i - 1];
      }

    // The order in each rank: the first rank as written, the others by their barycentres,
    // then sweeps down and up, the order with the fewest crossings kept.

    $pos = [];

    foreach ( $ranks as $r => $list )
      foreach ( $list as $i => $id )
        $pos [$id] = $i;

    $best  = $ranks;
    $least = padDiagramCrossings ( $ranks, $down, $pos );

    for ( $sweep = 0; $sweep < 12 and $least > 0; $sweep++ ) {

      $order = ( $sweep % 2 == 0 ) ? array_keys ( $ranks ) : array_reverse ( array_keys ( $ranks ) );

      foreach ( $order as $r ) {

        $near = ( $sweep % 2 == 0 ) ? $up : $down;
        $keys = [];

        foreach ( $ranks [$r] as $i => $id ) {
          $sum = 0;
          foreach ( $near [$id] ?? [] as $other )
            $sum += $pos [$other];
          $keys [$id] = isset ( $near [$id] ) ? [ $sum / count ( $near [$id] ), $i ] : [ $i, $i ];
        }

        usort ( $ranks [$r], fn ( $a, $b ) => $keys [$a] <=> $keys [$b] );

        foreach ( $ranks [$r] as $i => $id )
          $pos [$id] = $i;

      }

      $count = padDiagramCrossings ( $ranks, $down, $pos );

      if ( $count < $least ) {
        $least = $count;
        $best  = $ranks;
      }

    }

    $ranks = $best;

    foreach ( $ranks as $r => $list )
      foreach ( $list as $i => $id )
        $pos [$id] = $i;

    // A loop of a node on itself stands to its right - a wider one for each further loop -
    // with its label beside it: the room it takes is kept free, beside the node in its rank
    // (TB, BT) or in the gap after its rank (LR, RL), where it was drawn over the next node.
    // In TB and BT every label of a node's loops stands past the outermost one, so the room
    // reaches past that loop by the widest label. The texts by the ends of a loop - the
    // multiplicities of a class on itself - stand beside the node over and under the loop,
    // and the room reaches past them too.

    // In LR and RL the texts at the starts of a node's loops stand over the loops and the
    // plates of their labels over those (padDiagramGraph): the height they add is kept free
    // above the node, toward the one before it in its rank.

    $loopRoom = $loopCount = $loopWide = $loopTails = $loopUp = [];

    foreach ( $edges as $edge )
      if ( $edge [0] === $edge [1] ) {
        $a              = $edge [0];
        $k              = $loopCount [$a] = ( $loopCount [$a] ?? -1 ) + 1;
        $wide           = $edge [2] !== '' ? padDiagramWidth ( $edge [2] ) + 8 : 0;
        $loopWide [$a]  = max ( $loopWide [$a] ?? 0, $wide );
        $loopRoom [$a]  = $vertical ? 30 + 22 * $k + $loopWide [$a] : max ( $loopRoom [$a] ?? 0, 30 + 22 * $k + $wide );
        foreach ( $edge ['ends'] ?? [] as $end => $text )
          if ( $text !== '' ) {
            $loopRoom [$a]  = max ( $loopRoom [$a], padDiagramWidth ( $text ) + 10 );
            $loopTails [$a] = ( $loopTails [$a] ?? 0 ) + ( $end ? 0 : 1 );
          }
      }

    if ( ! $vertical )
      foreach ( $loopTails as $a => $tails )
        if ( $tails )
          $loopUp [$a] = 12 * $tails + 2 * $loopCount [$a] + 3;

    // The main axis: each rank as deep as its deepest node, the gaps between as wide as
    // their labels ask.

    $main = [];
    $at   = 0;

    foreach ( $ranks as $r => $list ) {

      $depth = 0;
      foreach ( $list as $id )
        $depth = max ( $depth, $vertical ? $nodes [$id] ['h'] : $nodes [$id] ['w'] );

      $gap = $gaps [$r] ?? 44;

      if ( ! $vertical )
        foreach ( $list as $id )
          if ( isset ( $loopRoom [$id] ) )
            $gap = max ( $gap, $nodes [$id] ['w'] / 2 + $loopRoom [$id] - $depth / 2 + 10 );

      $main [$r] = [ $at, $depth ];
      $at       += $depth + $gap;

    }

    foreach ( $ranks as $r => $list )
      foreach ( $list as $id )
        $nodes [$id] ['main'] = $main [$r] [0] + $main [$r] [1] / 2;

    // The cross axis: packed, then each rank moved toward its neighbours, down and up.

    $size  = fn ( $id ) => $vertical ? $nodes [$id] ['w'] : $nodes [$id] ['h'];
    $space = fn ( $a, $b ) => ( $size ( $a ) + $size ( $b ) ) / 2 + ( ( $nodes [$a] ['shape'] == 'dummy' or $nodes [$b] ['shape'] == 'dummy' ) ? 14 : 30 )
                            + ( $vertical ? ( $loopRoom [$a] ?? 0 ) : ( $loopUp [$b] ?? 0 ) );
    $cross = [];

    foreach ( $ranks as $list ) {
      $x = 0;
      foreach ( $list as $i => $id ) {
        $x           += $i ? $space ( $list [$i - 1], $id ) : 0;
        $cross [$id]  = $x;
      }
    }

    for ( $round = 0; $round < 10; $round++ )
      foreach ( [ $up, $down ] as $k => $near ) {

        $order = $k ? array_reverse ( array_keys ( $ranks ) ) : array_keys ( $ranks );

        foreach ( $order as $r ) {

          $list = $ranks [$r];
          $want = [];

          foreach ( $list as $id ) {
            $sum = 0;
            foreach ( $near [$id] ?? [] as $other )
              $sum += $cross [$other];
            $want [] = isset ( $near [$id] ) ? $sum / count ( $near [$id] ) : $cross [$id];
          }

          foreach ( padDiagramPlace ( $list, $want, $space ) as $i => $x )
            $cross [ $list [$i] ] = $x;

        }

      }

    $low = PHP_INT_MAX;

    foreach ( $cross as $id => $x )
      $low = min ( $low, $x - $size ( $id ) / 2 );

    foreach ( $cross as $id => $x )
      $nodes [$id] ['cross'] = $x - $low;

    return [ $chains, $turned, $ranks, $main ];

  }

  // The crossings between every two ranks next to each other, counted pair by pair.

  function padDiagramCrossings ( $ranks, $down, $pos ) {

    $count = 0;

    foreach ( $ranks as $list ) {

      $lines = [];

      foreach ( $list as $id )
        foreach ( $down [$id] ?? [] as $to )
          $lines [] = [ $pos [$id], $pos [$to] ];

      foreach ( $lines as $i => $a )
        for ( $j = $i + 1; $j < count ( $lines ); $j++ )
          if ( ( $a [0] - $lines [$j] [0] ) * ( $a [1] - $lines [$j] [1] ) < 0 )
            $count++;

    }

    return $count;

  }

  // The positions of a rank nearest the wanted ones that keep the order and the spacing:
  // taking off each node's least offset from the first makes the spacing an order, and the
  // pool adjacent violators algorithm finds the nearest ordered values.

  function padDiagramPlace ( $list, $want, $space ) {

    $offset = [ 0 ];

    for ( $i = 1; $i < count ( $list ); $i++ )
      $offset [$i] = $offset [$i - 1] + $space ( $list [$i - 1], $list [$i] );

    $blocks = [];

    foreach ( $want as $i => $x ) {

      $blocks [] = [ $x - $offset [$i], 1 ];

      while ( count ( $blocks ) > 1 ) {
        $last = count ( $blocks ) - 1;
        if ( $blocks [$last - 1] [0] <= $blocks [$last] [0] )
          break;
        $n = $blocks [$last - 1] [1] + $blocks [$last] [1];
        $blocks [$last - 1] = [ ( $blocks [$last - 1] [0] * $blocks [$last - 1] [1] + $blocks [$last] [0] * $blocks [$last] [1] ) / $n, $n ];
        array_pop ( $blocks );
      }

    }

    $place = [];
    $i     = 0;

    foreach ( $blocks as list ( $value, $n ) )
      for ( $k = 0; $k < $n; $k++, $i++ )
        $place [$i] = $value + $offset [$i];

    return $place;

  }

  // The SVG of a flowchart. $direction is TB (top to bottom), BT, LR (left to right) or RL;
  // $edgeForm draws the edges orthogonal - down, across in a channel of their own, down -
  // straight, or curved.

  function padDiagramFlow ( $source, $direction, $edgeForm, $title ) {

    global $padCheckSyntax;

    list ( $written, $nodes, $edges ) = padDiagramParse ( $source, $bad );

    if ( $bad and $padCheckSyntax )
      padError ( 'the diagram cannot read line ' . $bad [0] [0] . ": '" . padMakeSafe ( $bad [0] [1], 60 ) . "' - write A --> B, A -->|label| B, A[Box], B{Question?} or C((Round))" );

    if ( ! $nodes )
      return '';

    if ( $direction === '' )
      $direction = $written !== '' ? $written : 'TB';

    if ( $direction == 'TD' )
      $direction = 'TB';

    $desc = [];

    foreach ( $nodes as $id => list ( $text, $shape ) ) {
      $lines        = padDiagramWrap ( $text );
      list ( $w, $h ) = padDiagramSize ( $lines, $shape );
      $nodes [$id]  = [ 'text' => $text, 'shape' => $shape, 'lines' => $lines, 'w' => $w, 'h' => $h ];
    }

    foreach ( $edges as list ( $a, $b, $label ) )
      $desc [] = padDiagramFlat ( $nodes [$a] ['text'] ) . ' → ' . padDiagramFlat ( $nodes [$b] ['text'] ) . ( $label !== '' ? " ($label)" : '' );

    // A node of no edge is told too - alone, it was left out of the description of a chart
    // with any edge, and a screen reader never heard of it.

    $linked = [];
    foreach ( $edges as list ( $a, $b ) )
      $linked [$a] = $linked [$b] = TRUE;

    foreach ( $nodes as $id => $node )
      if ( ! isset ( $linked [$id] ) )
        $desc [] = padDiagramFlat ( $node ['text'] );

    return padDiagramGraph ( $nodes, $edges, $desc, $direction, $edgeForm, $title );

  }

  // The SVG of a diagram in layers - the flowchart, the state diagram, the class and the
  // entity-relationship diagram. A node is [ 'shape', 'lines', 'w', 'h' ] by its id, an edge
  // [ from, to, label, style, arrow ] in the order of the text, and $desc says the edges in
  // words, in that order, then whatever else there is to say. $direction is TB (top to
  // bottom), BT, LR (left to right) or RL; $edgeForm draws the edges orthogonal - down,
  // across in a channel of their own, down - straight, or curved; $css adds the rules of a
  // kind's own shapes.
  //
  // An edge may say more by name: 'head' and 'tail', the marks at its ends (padDiagramMark -
  // an arrow, a triangle, a diamond, the feet of a crow ...), else an arrow when it has one,
  // and 'ends', the texts written by its two ends, [ at the tail, at the head ]. A node of a
  // shape the flowchart does not have is drawn by padDiagramDraw<Shape>, its kind's own.
  //
  // $room asks for more room than a flowchart needs - 'gap', the least gap between two
  // ranks, and 'spread', the distance between the edges leaving one side of a node - for the
  // marks at the ends: the feet of a crow are 16 wide and 18 long, and on a flowchart's 14
  // and the 15 before an edge's first turn the feet of two edges lay on each other.

  function padDiagramGraph ( $nodes, $edges, $desc, $direction, $edgeForm, $title, $css = '', $room = [] ) {

    $vertical = in_array ( $direction, [ 'TB', 'BT' ] );

    // A gap as wide as the labels of the edges that leave the rank before it: in LR the
    // text lies along the edge.

    $ranking = padDiagramRanking ( $nodes, $edges );
    $rankOf  = $ranking [0];
    $gaps    = [];

    if ( isset ( $room ['gap'] ) )
      foreach ( $rankOf as $r )
        $gaps [$r] = $room ['gap'];

    // Every rank an edge crosses is a node of its own in the layout. Past twenty thousand -
    // 0.4 s and 46 MB; a chain of a thousand steps with an edge from the first to each is
    // half a million - the layout ran out of memory instead of saying so.

    $crossed = 0;
    foreach ( $edges as list ( $a, $b ) )
      $crossed += max ( 0, abs ( $rankOf [$a] - $rankOf [$b] ) - 1 );

    if ( $crossed > 20000 ) {
      padError ( "the diagram is too large to lay out: its edges cross $crossed ranks between their ends, more than 20000 - draw it in parts" );
      return '';
    }

    // Ordering the ranks weighs every pair of edges between two neighbouring ranks against
    // each other, time with the square of their number: & joins names by the hundred, and
    // one line of 1.9 KB - A1 & ... & A150 --> B1 & ... & B150, 22,500 edges, no rank crossed
    // - took 80 seconds. The squares add up to no more than 2,500 edges between two ranks
    // make, about a second.

    $between = [];

    foreach ( $edges as list ( $a, $b ) )
      for ( $r = min ( $rankOf [$a], $rankOf [$b] ); $r < max ( $rankOf [$a], $rankOf [$b] ); $r++ )
        $between [$r] = ( $between [$r] ?? 0 ) + 1;

    if ( array_sum ( array_map ( fn ( $count ) => $count * $count, $between ) ) > 2500 * 2500 ) {
      padError ( 'the diagram is too large to lay out: ' . max ( $between ) . ' of its edges run between the same two ranks, '
               . array_sum ( $between ) . ' between all of them - ordering that many takes too long, 2500 between two ranks at most - draw it in parts' );
      return '';
    }

    foreach ( $edges as list ( $a, $b, $label ) )
      if ( $label !== '' and $a !== $b ) {
        $r    = min ( $rankOf [$a], $rankOf [$b] );
        $need = $vertical ? 60 : padDiagramWidth ( $label ) + 36;
        $gaps [$r] = max ( $gaps [$r] ?? 44, $need );
      }

    // The texts by the ends of an edge - multiplicities - stand along it beside the label:
    // the gap makes room for the three, where the label's plate covered the one at its head.

    foreach ( $edges as $edge )
      if ( ( $edge ['ends'] ?? [ '', '' ] ) != [ '', '' ] and $edge [0] !== $edge [1] ) {
        $r     = min ( $rankOf [ $edge [0] ], $rankOf [ $edge [1] ] );
        $label = $edge [2] !== '' ? padDiagramWidth ( $edge [2] ) + 8 : 0;
        $ends  = padDiagramWidth ( $edge ['ends'] [0] ) + padDiagramWidth ( $edge ['ends'] [1] );
        $need  = $vertical ? ( $label ? 90 : 76 ) : $label + $ends + 70;
        $gaps [$r] = max ( $gaps [$r] ?? 44, $need );
      }

    list ( $chains, $turned ) = padDiagramLayout ( $nodes, $edges, $vertical, $gaps, $ranking );

    // The ports: the edges leaving a node spread along its side, in the order of where they
    // go; so are those coming in. A dummy has one of each, in its middle.

    $outs = $ins = [];

    foreach ( $chains as $e => $chain )
      for ( $i = 1; $i < count ( $chain ); $i++ ) {
        $outs [ $chain [$i - 1] ] [] = [ $e, $i ];
        $ins  [ $chain [$i] ]     [] = [ $e, $i ];
      }

    $port = [];

    foreach ( [ 'out' => $outs, 'in' => $ins ] as $side => $list )
      foreach ( $list as $id => $segments ) {

        $other = ( $side == 'out' ) ? 0 : -1;
        $where = fn ( $s ) => [ $nodes [ $chains [ $s [0] ] [ $s [1] + $other ] ] ['cross'], $s [0] ];

        usort ( $segments, fn ( $p, $q ) => $where ( $p ) <=> $where ( $q ) );

        $k     = count ( $segments );
        $width = $vertical ? $nodes [$id] ['w'] : $nodes [$id] ['h'];
        $step  = ( $k > 1 ) ? min ( $room ['spread'] ?? 14, $width * ( isset ( $room ['spread'] ) ? 0.8 : 0.6 ) / ( $k - 1 ) ) : 0;

        foreach ( $segments as $j => list ( $e, $i ) )
          $port [$side] [$e] [$i] = ( $nodes [$id] ['shape'] == 'dummy' ) ? 0 : ( $j - ( $k - 1 ) / 2 ) * $step;

      }

    // The segment between two ranks: from the port at the foot of one node to the port at
    // the head of the next, in layout space - main down, cross along.

    $segment = function ( $e, $i ) use ( $chains, $nodes, $port, $vertical ) {
      $a  = $nodes [ $chains [$e] [$i - 1] ];
      $b  = $nodes [ $chains [$e] [$i] ];
      $oa = $port ['out'] [$e] [$i];
      $ob = $port ['in']  [$e] [$i];
      return [ [ $a ['cross'] + $oa, $a ['main'] + padDiagramReach ( $a, $oa, $vertical ) ],
               [ $b ['cross'] + $ob, $b ['main'] - padDiagramReach ( $b, $ob, $vertical ) ] ];
    };

    // The channels: the segments of a gap that must go across get a line of their own, the
    // ones going right ordered from the right, the ones going left from the left, so a turn
    // does not cut through another edge's way down. A gap is the two ranks it lies between,
    // not where a segment starts: from the sloping outline of a diamond or a circle every
    // segment started elsewhere, was alone in its "gap", and the runs of five branches lay
    // a pixel apart, one line.

    $bends = $gapSegments = [];

    foreach ( $chains as $e => $chain )
      for ( $i = 1; $i < count ( $chain ); $i++ ) {
        list ( $p, $q ) = $segment ( $e, $i );
        if ( abs ( $p [0] - $q [0] ) > 1 )
          $gapSegments [ round ( $nodes [ $chain [$i - 1] ] ['main'] ) . ':' . round ( $nodes [ $chain [$i] ] ['main'] ) ] [] = [ $e, $i, $p, $q ];
      }

    foreach ( $gapSegments as $list ) {

      usort ( $list, function ( $s, $t ) {
        $sr = $s [3] [0] > $s [2] [0];
        $tr = $t [3] [0] > $t [2] [0];
        if ( $sr != $tr )
          return $sr ? -1 : 1;
        return $sr ? [ -$s [2] [0], $s [0] ] <=> [ -$t [2] [0], $t [0] ] : [ $s [2] [0], $s [0] ] <=> [ $t [2] [0], $t [0] ];
      } );

      $top    = max ( array_map ( fn ( $s ) => $s [2] [1], $list ) );
      $bottom = min ( array_map ( fn ( $s ) => $s [3] [1], $list ) );

      foreach ( $list as $k => list ( $e, $i ) )
        $bends [$e] [$i] = $top + ( $k + 1 ) * ( $bottom - $top ) / ( count ( $list ) + 1 );

    }

    // Every edge as a list of points, in layout space, then turned to the direction asked.

    $flip = function ( $point ) use ( $direction ) {
      list ( $c, $m ) = $point;
      switch ( $direction ) {
        case 'BT': return [ $c, -$m ];
        case 'LR': return [ $m, $c ];
        case 'RL': return [ -$m, $c ];
      }
      return [ $c, $m ];
    };

    $paths = [];

    foreach ( $chains as $e => $chain ) {

      $points = [];

      for ( $i = 1; $i < count ( $chain ); $i++ ) {

        list ( $p, $q ) = $segment ( $e, $i );

        if ( ! $points )
          $points [] = $p;

        if ( $edgeForm == 'orthogonal' and isset ( $bends [$e] [$i] ) ) {
          $points [] = [ $p [0], $bends [$e] [$i] ];
          $points [] = [ $q [0], $bends [$e] [$i] ];
        }

        $points [] = $q;

      }

      if ( isset ( $turned [$e] ) )
        $points = array_reverse ( $points );

      $paths [$e] = array_map ( $flip, $points );

    }

    // The bounds of the nodes, the edges and the self loops, to lay the picture out from.

    $boxes = [];

    foreach ( $nodes as $id => $node )
      if ( $node ['shape'] != 'dummy' ) {
        list ( $x, $y ) = $flip ( [ $node ['cross'], $node ['main'] ] );
        $nodes [$id] ['x'] = $x;
        $nodes [$id] ['y'] = $y;
        $boxes [] = [ $x - $node ['w'] / 2, $y - $node ['h'] / 2, $x + $node ['w'] / 2, $y + $node ['h'] / 2 ];
      }

    // A loop stands on the side its rank's flow goes on to - the right, in RL the left - where
    // the room for it is kept: in RL it was drawn on the right, under the node before, and
    // the room kept on the left stood empty.

    // A loop's label stands beside the loop on a plate, as an edge's label does, and the
    // labels of the edges step aside for it: in LR the label of the edge leaving the node
    // was put half way along it, in the room kept for the loop, over the loop's label -
    // "yes" showed, "retry" not - and an edge ran through the text. In LR and RL the edge to
    // the next rank leaves the node's middle on the loop's side, so the plate stands above
    // the loop, one over the other for further loops: on the middle line it was read as that
    // edge's label, and the edge's own label, stepping aside, covered its arrowhead.

    // The loop itself is in the way of a label as well - [ x, y, width, half height ] around
    // its curve: with the loop's label above it, the label of the edge leaving the node on the
    // loop's side was put half way along that edge, on the loop's turn, and the loop seemed to
    // run into "yes" and out again.

    // In TB and BT the labels of a node's loops stand past its outermost loop, one under the
    // other: each past its own loop at the node's middle, two labelled loops on one node had
    // their plates on one spot, the first also over the second loop's turn.

    // The texts by the ends of a loop stand beside the node where the loop leaves it, the
    // one at its start over the loop and its mark, the one at its end under them - one over
    // or under the other for further loops, all loops of a node leaving it on one spot - and
    // in LR and RL the plates of the labels stand over those texts.

    $side   = ( $direction == 'RL' ) ? -1 : 1;
    $loops  = $labels = $curves = $loopsOf = $tailsOf = $loopEnds = $endsAt = [];

    foreach ( $edges as $edge )
      if ( $edge [0] === $edge [1] ) {
        $loopsOf [ $edge [0] ] = ( $loopsOf [ $edge [0] ] ?? 0 ) + 1;
        if ( ( $edge ['ends'] [0] ?? '' ) !== '' )
          $tailsOf [ $edge [0] ] = ( $tailsOf [ $edge [0] ] ?? 0 ) + 1;
      }

    foreach ( $edges as $e => $edge )
      if ( $edge [0] === $edge [1] ) {
        list ( $a, , $label ) = $edge;
        $k          = $loops [$a] = ( $loops [$a] ?? -1 ) + 1;
        $n          = $nodes [$a];
        $far        = $n ['x'] + $side * ( $n ['w'] / 2 + 30 + 22 * $k + ( $label !== '' ? padDiagramWidth ( $label ) + 8 : 0 ) );
        $boxes []   = [ min ( $n ['x'], $far ), $n ['y'] - 28 - 6 * $k, max ( $n ['x'], $far ), $n ['y'] + 28 + 6 * $k ];
        list ( $x, $r ) = padDiagramLoopAt ( $n, $k, $side );
        $curves []  = [ $x + $r * 0.375, $n ['y'], abs ( $r ) * 0.75, 15 + 2 * $k, 'loop' ];
        $tails      = $tailsOf [$a] ?? 0;
        if ( $label !== '' ) {
          $w              = padDiagramWidth ( $label ) + 8;
          $outer          = padDiagramLoopAt ( $n, $loopsOf [$a] - 1, $side ) [1];
          $labels [$e]    = $vertical ? [ $x + $outer * 0.75 + $side * $w / 2, $n ['y'] + 21 * ( $k - ( $loopsOf [$a] - 1 ) / 2 ), $w ]
                                      : [ $x + $side * $w / 2, $n ['y'] - 28 - 21 * $k - ( $tails ? 12 * $tails + 2 * $loopsOf [$a] + 1 : 0 ), $w ];
          $boxes []       = [ $labels [$e] [0] - $w / 2, $labels [$e] [1] - 9, $labels [$e] [0] + $w / 2, $labels [$e] [1] + 9 ];
        }
        foreach ( [ 0 => -1, 1 => 1 ] as $end => $way )
          if ( ( $text = $edge ['ends'] [$end] ?? '' ) !== '' ) {
            $j                 = $endsAt [$a] [$end] = ( $endsAt [$a] [$end] ?? -1 ) + 1;
            $base              = $n ['y'] + $way * ( ( $end ? 29 : 22 ) + 2 * ( $loopsOf [$a] - 1 ) + 12 * $j );
            $loopEnds [$e] []  = [ $text, $x + $side * 4, $base ];
            $boxes []          = [ min ( $x, $x + $side * ( padDiagramWidth ( $text ) + 4 ) ), $base - 10, max ( $x, $x + $side * ( padDiagramWidth ( $text ) + 4 ) ), $base + 2 ];
          }
      }

    $loops = [];

    foreach ( $paths as $points )
      foreach ( $points as list ( $x, $y ) )
        $boxes [] = [ $x, $y, $x, $y ];

    // What a label keeps clear of - the loops, then the labels in the order they are placed -
    // in a grid of cells (padDiagramTake), so a spot is weighed against the few near it:
    // weighed against all of them at every step of every label, 800 labelled edges between
    // two nodes took 8 seconds and 1600 a minute. The first of them in that order that a spot
    // touches is the one it steps past, as before, so every label stands where it stood.

    $taken = [ 'all' => [], 'grid' => [], 'jumps' => [] ];

    foreach ( array_merge ( $curves, array_values ( $labels ) ) as $one )
      padDiagramTake ( $taken, $one );

    // The labels sit half way along their edge, on a plate of the background.

    foreach ( $edges as $e => list ( $a, $b, $label ) )
      if ( $label !== '' and isset ( $paths [$e] ) ) {

        $w = padDiagramWidth ( $label ) + 8;

        // Half way, else a little before or after where another label or a loop stands there:
        // stepped aside from the first loop it met, a label that only neared a loop's turn half
        // way along was pushed below it and on past other labels, far from its edge.

        $carrier = NULL;

        foreach ( $chains [$e] ?? [] as $one )
          if ( ( $nodes [$one] ['carries'] ?? NULL ) === $e )
            $carrier = $one;

        if ( $carrier !== NULL ) {
          list ( $x, $y ) = $flip ( [ $nodes [$carrier] ['cross'], $nodes [$carrier] ['main'] ] );
          $other = padDiagramClash ( $taken, $x, $y, $w );
        } else
          foreach ( [ 0.5, 0.35, 0.65, 0.22, 0.78, 0.5 ] as $part ) {
            list ( $x, $y ) = padDiagramMiddle ( $paths [$e], $part );
            if ( ( $other = padDiagramClash ( $taken, $x, $y, $w ) ) === NULL )
              break;
          }

        // Nowhere along its edge: beside the label in the way, across the flow - and beside
        // the next one in the way, on the same side, until it stands free: moved once, it
        // could land on a label moved there before, and the two were written on one spot.

        if ( $other !== NULL )
          list ( $x, $y ) = padDiagramStep ( $taken, $other, $x, $y, $w, $vertical );

        $labels [$e] = [ $x, $y, $w ];
        $boxes []    = [ $x - $w / 2, $y - 9, $x + $w / 2, $y + 9 ];

        padDiagramTake ( $taken, $labels [$e] );

      }

    $minX = min ( array_column ( $boxes, 0 ) ) - 12;
    $minY = min ( array_column ( $boxes, 1 ) ) - 12;
    $maxX = max ( array_column ( $boxes, 2 ) ) + 12;
    $maxY = max ( array_column ( $boxes, 3 ) ) + 12;

    $width  = (int) ceil ( $maxX - $minX );
    $height = (int) ceil ( $maxY - $minY );
    $X      = fn ( $x ) => padChartXY ( $x - $minX );
    $Y      = fn ( $y ) => padChartXY ( $y - $minY );

    list ( $id, $svg ) = padChartOpen ( 'diagram', [ $nodes, $edges, $direction, $edgeForm ], $desc, $title, $width, $height, FALSE );

    $svg .= padDiagramStyle () . $css;

    // The edges first, the nodes over their ends.

    foreach ( $edges as $e => $edge ) {

      list ( $a, $b, $label, $style, $arrow ) = $edge;

      $head  = $edge ['head'] ?? ( $arrow ? 'arrow' : '' );
      $tail  = $edge ['tail'] ?? '';
      $class = 'pd-edge' . ( $style != 'solid' ? " pd-$style" : '' );
      $tip   = '<title>' . padChartAttr ( $desc [$e] ) . '</title>';

      if ( $a === $b ) {

        // A loop on the right of its node - in RL on the left - a wider one for every further
        // loop there.

        $k  = $loops [$a] = ( $loops [$a] ?? -1 ) + 1;
        $n  = $nodes [$a];
        $y  = $n ['y'];

        list ( $x, $r ) = padDiagramLoopAt ( $n, $k, $side );

        // The mark at its tail where it leaves the node, as an edge between two nodes has it:
        // a relation of a class to itself - a tree of nodes, a manager - lost its diamond,
        // and one of an entity to itself the bars of its exactly one, while the description
        // said them. The texts by its ends stand beside the node, over and under the loop.

        $d  = 'M' . $X ( $x ) . ',' . $Y ( $y - 8 ) . 'C' . $X ( $x + $r ) . ',' . $Y ( $y - 28 - 6 * $k ) . ' ' . $X ( $x + $r ) . ',' . $Y ( $y + 28 + 6 * $k ) . ' ' . $X ( $x + 6 * $side ) . ',' . $Y ( $y + 10 );
        $svg .= "<g class=\"pc-mark\">$tip<path class=\"$class\" d=\"$d\"/>"
              . padDiagramMark ( $head, [ $x + 12 * $side, $y + 14 ], [ $x, $y + 8 ], $X, $Y, $style )
              . padDiagramMark ( $tail, [ $x + 12 * $side, $y - 14 ], [ $x, $y - 8 ], $X, $Y, $style ) . '</g>';

        foreach ( $loopEnds [$e] ?? [] as list ( $text, $tx, $ty ) )
          $svg .= '<text class="pd-end" x="' . $X ( $tx ) . '" y="' . $Y ( $ty ) . '" text-anchor="' . ( $side > 0 ? 'start' : 'end' ) . '">' . padChartAttr ( $text ) . '</text>';

        continue;

      }

      $points = $paths [$e];
      $last   = end ( $points );
      $before = $points [ count ( $points ) - 2 ];
      $first  = $points [0];
      $after  = $points [1];

      // A curve arrives along the flow - its last control point lies straight before its
      // end - so its head points that way and it is shortened along it: taken from the
      // chord, the head lay sideways on the box and the curve ended off its base. It leaves
      // along the flow as well, which is where a mark at its tail points.

      if ( $edgeForm == 'curved' ) {
        $along = $vertical ? [ $last [0], ( $before [1] + $last [1] ) / 2 ] : [ ( $before [0] + $last [0] ) / 2, $last [1] ];
        if ( $along != $last )
          $before = $along;
        $along = $vertical ? [ $first [0], ( $after [1] + $first [1] ) / 2 ] : [ ( $after [0] + $first [0] ) / 2, $first [1] ];
        if ( $along != $first )
          $after = $along;
      }

      if ( $cut = padDiagramMarkCut ( $head, $style ) )
        $points [ count ( $points ) - 1 ] = padDiagramShorten ( $before, $last, $cut );

      if ( $cut = padDiagramMarkCut ( $tail, $style ) )
        $points [0] = padDiagramShorten ( $after, $first, $cut );

      $svg .= "<g class=\"pc-mark\">$tip<path class=\"$class\" d=\"" . padDiagramPath ( $points, $edgeForm, $vertical, $X, $Y ) . '"/>'
            . padDiagramMark ( $head, $before, $last, $X, $Y, $style ) . padDiagramMark ( $tail, $after, $first, $X, $Y, $style ) . '</g>';

      // The texts by its ends - the multiplicities of a class diagram - stand beside the
      // line, a little way along it from the node.

      foreach ( [ [ $edge ['ends'] [0] ?? '', $after, $first ], [ $edge ['ends'] [1] ?? '', $before, $last ] ] as list ( $text, $from, $to ) )
        if ( $text !== '' )
          $svg .= padDiagramEndText ( $text, $from, $to, $X, $Y );

    }

    foreach ( $labels as $e => list ( $x, $y, $w ) )
      $svg .= '<rect class="pd-plate" x="' . $X ( $x - $w / 2 ) . '" y="' . $Y ( $y - 9 ) . '" width="' . padChartXY ( $w ) . '" height="18" rx="3"/>'
            . '<text x="' . $X ( $x ) . '" y="' . $Y ( $y + 4 ) . '" text-anchor="middle">' . padChartAttr ( $edges [$e] [2] ) . '</text>';

    foreach ( $nodes as $node )
      if ( $node ['shape'] != 'dummy' )
        $svg .= padDiagramNode ( $node, $X, $Y, $minX, $minY );

    return "$svg</svg>";

  }

  // A spot a label keeps clear of, taken: [ x, y, width, half height, kind ] - a label is
  // 9 high either side of its middle, a loop as high as its curve - kept in the order
  // taken, and in every cell of 64 by 32 its box touches.

  function padDiagramTake ( &$taken, $one ) {

    $one += [ 3 => 9, 4 => '' ];
    $i    = count ( $taken ['all'] );

    $taken ['all'] [] = $one;

    for ( $cx = (int) floor ( ( $one [0] - $one [2] / 2 ) / 64 ); $cx <= (int) floor ( ( $one [0] + $one [2] / 2 ) / 64 ); $cx++ )
      for ( $cy = (int) floor ( ( $one [1] - $one [3] ) / 32 ); $cy <= (int) floor ( ( $one [1] + $one [3] ) / 32 ); $cy++ )
        $taken ['grid'] [$cx] [$cy] [] = $i;

  }

  // The first spot taken, in the order taken, that a label $w wide at x, y would touch - it
  // keeps 2 clear of another's side and 11 of its middle line - as its number, or NULL:
  // only the cells within reach are looked in, a pixel further than the label reaches, so
  // a rounding in the cells never loses a spot the test itself would find.

  function padDiagramClash ( $taken, $x, $y, $w ) {

    $first = NULL;

    for ( $cx = (int) floor ( ( $x - $w / 2 - 3 ) / 64 ); $cx <= (int) floor ( ( $x + $w / 2 + 3 ) / 64 ); $cx++ )
      for ( $cy = (int) floor ( ( $y - 12 ) / 32 ); $cy <= (int) floor ( ( $y + 12 ) / 32 ); $cy++ )
        foreach ( $taken ['grid'] [$cx] [$cy] ?? [] as $i )
          if ( $first === NULL or $i < $first ) {
            list ( $ox, $oy, $ow, $oh ) = $taken ['all'] [$i];
            if ( abs ( $ox - $x ) < ( $ow + $w ) / 2 + 2 and abs ( $oy - $y ) < $oh + 11 )
              $first = $i;
          }

    return $first;

  }

  // Where a label $w wide stands that meets the spot $other at x, y: past it, across the
  // flow - in TB and BT beside it, in LR and RL under or over it, on the side the label was
  // nearer - and past the next one in the way there, on the same side, until it stands free.
  //
  // A step always goes on past the spot in the way, never back, so a spot is passed once,
  // and where a label lands after passing one is the same for every label of that width on
  // that line across the flow: the next spot in the way there is the first one taken that
  // touches it, and a spot taken later comes after it. Which spot follows which is kept, by
  // the side, the width and the line - and the labels that meet the same spot after it go on
  // in one jump to the last spot passed: every label of 2500 between two nodes passed the
  // ones before it one at a time, and the three million steps took four seconds.

  function padDiagramStep ( &$taken, $other, $x, $y, $w, $vertical ) {

    $sign  = $vertical ? ( $x >= $taken ['all'] [$other] [0] ? 1 : -1 ) : ( $y >= $taken ['all'] [$other] [1] ? 1 : -1 );
    $line  = $sign . ':' . $w . ':' . pack ( 'd', $vertical ? $y : $x );
    $jumps = $taken ['jumps'] [$line] ?? [];
    $path  = [];

    unset ( $taken ['jumps'] [$line] );

    while ( TRUE ) {

      while ( isset ( $jumps [$other] ) ) {
        $path [] = $other;
        $other   = $jumps [$other];
      }

      list ( $ox, $oy, $ow, $oh ) = $taken ['all'] [$other];

      if ( $vertical )
        $x = $ox + $sign * ( ( $ow + $w ) / 2 + 4 );
      else
        $y = $oy + $sign * ( $oh + 12 );

      $next = padDiagramClash ( $taken, $x, $y, $w );

      if ( $next === NULL )
        break;

      $path []        = $other;
      $jumps [$other] = $next;
      $other          = $next;

    }

    foreach ( $path as $one )
      $jumps [$one] = $other;

    $taken ['jumps'] [$line] = $jumps;

    return [ $x, $y ];

  }

  // A node at its place: the shapes of the flowchart here, any other one by the function its
  // kind has for it - padDiagramDrawState for a state - which is handed the same.

  function padDiagramNode ( $node, $X, $Y, $minX, $minY ) {

    list ( $x, $y, $w, $h ) = [ $node ['x'], $node ['y'], $node ['w'], $node ['h'] ];

    $svg = '';

    if ( $node ['shape'] == 'circle' )
      $svg .= '<circle class="pd-node pd-circle" cx="' . $X ( $x ) . '" cy="' . $Y ( $y ) . '" r="' . padChartXY ( $w / 2 ) . '"/>';
    elseif ( $node ['shape'] == 'diamond' )
      $svg .= '<path class="pd-node pd-diamond" d="M' . $X ( $x ) . ',' . $Y ( $y - $h / 2 ) . 'L' . $X ( $x + $w / 2 ) . ',' . $Y ( $y )
            . 'L' . $X ( $x ) . ',' . $Y ( $y + $h / 2 ) . 'L' . $X ( $x - $w / 2 ) . ',' . $Y ( $y ) . 'Z"/>';
    elseif ( $node ['shape'] == 'hexagon' )
      $svg .= '<path class="pd-node pd-diamond" d="M' . $X ( $x - $w / 2 ) . ',' . $Y ( $y ) . 'L' . $X ( $x - $w / 2 + $h / 3 ) . ',' . $Y ( $y - $h / 2 )
            . 'H' . $X ( $x + $w / 2 - $h / 3 ) . 'L' . $X ( $x + $w / 2 ) . ',' . $Y ( $y ) . 'L' . $X ( $x + $w / 2 - $h / 3 ) . ',' . $Y ( $y + $h / 2 )
            . 'H' . $X ( $x - $w / 2 + $h / 3 ) . 'Z"/>';
    elseif ( $node ['shape'] == 'database' ) {
      $rx   = padChartXY ( $w / 2 );
      $svg .= '<path class="pd-node pd-circle" d="M' . $X ( $x - $w / 2 ) . ',' . $Y ( $y - $h / 2 + 6 ) . "a$rx,6 0 0,0 " . padChartXY ( $w ) . ',0'
            . "a$rx,6 0 0,0 -" . padChartXY ( $w ) . ',0V' . $Y ( $y + $h / 2 - 6 ) . "a$rx,6 0 0,0 " . padChartXY ( $w ) . ',0V' . $Y ( $y - $h / 2 + 6 ) . '"/>';
    } elseif ( $node ['shape'] != 'box' and $node ['shape'] != 'round' and function_exists ( $draw = 'padDiagramDraw' . ucfirst ( $node ['shape'] ) ) )
      return $draw ( $node, $X, $Y, $minX, $minY );
    else
      $svg .= '<rect class="pd-node" x="' . $X ( $x - $w / 2 ) . '" y="' . $Y ( $y - $h / 2 ) . '" width="' . padChartXY ( $w ) . '" height="' . padChartXY ( $h )
            . '" rx="' . ( $node ['shape'] == 'round' ? padChartXY ( $h / 2 ) : 4 ) . '"/>';

    return $svg . padDiagramText ( $node ['lines'], $x - $minX, $y - $minY + ( $node ['shape'] == 'database' ? 5 : 0 ) );

  }

  // The mark at the end of an edge, at $to and pointing away from $from: an arrow (filled),
  // open (two strokes), a triangle (hollow - a class inherits), a diamond (filled - made
  // of) or an odiamond (hollow - holds), and the feet of the entity-relationship diagram:
  // one (two bars), zeroone (a bar and a ring), many (a crow's foot and a bar) and zeromany
  // (a crow's foot and a ring). '' is no mark.

  function padDiagramMark ( $mark, $from, $to, $X, $Y, $style = 'solid' ) {

    if ( $mark === '' )
      return '';

    if ( $mark == 'arrow' )
      return padDiagramHead ( $from, $to, $X, $Y, $style );

    $length = hypot ( $to [0] - $from [0], $to [1] - $from [1] ) ?: 1;
    $u      = [ ( $to [0] - $from [0] ) / $length, ( $to [1] - $from [1] ) / $length ];
    $n      = [ -$u [1], $u [0] ];

    // A point $back from the end along the edge and $side across it.

    $at = fn ( $back, $side = 0 ) => $X ( $to [0] - $u [0] * $back + $n [0] * $side ) . ',' . $Y ( $to [1] - $u [1] * $back + $n [1] * $side );

    switch ( $mark ) {

      case 'open':
        return '<path class="pd-edge" d="M' . $at ( 10, 5 ) . 'L' . $at ( 0 ) . 'L' . $at ( 10, -5 ) . '"/>';

      case 'triangle':
        return '<path class="pd-hollow" d="M' . $at ( 0 ) . 'L' . $at ( 13, 7 ) . 'L' . $at ( 13, -7 ) . 'Z"/>';

      case 'diamond':
      case 'odiamond':
        return '<path class="' . ( $mark == 'diamond' ? 'pd-head' : 'pd-hollow' ) . '" d="M' . $at ( 0 ) . 'L' . $at ( 8, 5 ) . 'L' . $at ( 16 ) . 'L' . $at ( 8, -5 ) . 'Z"/>';

    }

    // The feet: a crow's foot spreads onto the node; a bar and a ring stand before it.

    $bar  = fn ( $back ) => '<path class="pd-foot" d="M' . $at ( $back, 7 ) . 'L' . $at ( $back, -7 ) . '"/>';
    $ring = fn ( $back ) => '<circle class="pd-ring" cx="' . $X ( $to [0] - $u [0] * $back ) . '" cy="' . $Y ( $to [1] - $u [1] * $back ) . '" r="4.5"/>';
    $foot = '<path class="pd-foot" d="M' . $at ( 0, 8 ) . 'L' . $at ( 12 ) . 'L' . $at ( 0, -8 ) . 'M' . $at ( 0 ) . 'L' . $at ( 12 ) . '"/>';

    switch ( $mark ) {
      case 'one':      return $bar ( 6 ) . $bar ( 11 );
      case 'zeroone':  return $bar ( 6 ) . $ring ( 15 );
      case 'many':     return $foot . $bar ( 16 );
      case 'zeromany': return $foot . $ring ( 18 );
    }

    return '';

  }

  // How far an edge stops short of its end for the mark there: the line ends under the base
  // of an arrow, at the back of a triangle or a diamond, and runs on to the node under the
  // feet and the open arrow.

  function padDiagramMarkCut ( $mark, $style = 'solid' ) {

    switch ( $mark ) {
      case 'arrow':    return ( $style == 'thick' ) ? 9 : 7;
      case 'triangle': return 13;
      case 'diamond':
      case 'odiamond': return 16;
    }

    return 0;

  }

  // A short text by the end of an edge at $to - "1" or "0..*" - a little back from the node,
  // above a line that lies and left of one that stands, so the two ends of an edge have
  // theirs on one side.

  function padDiagramEndText ( $text, $from, $to, $X, $Y ) {

    $length = hypot ( $to [0] - $from [0], $to [1] - $from [1] ) ?: 1;
    $u      = [ ( $to [0] - $from [0] ) / $length, ( $to [1] - $from [1] ) / $length ];
    $n      = [ -$u [1], $u [0] ];

    if ( $n [1] > 0.001 or ( abs ( $n [1] ) <= 0.001 and $n [0] > 0 ) )
      $n = [ -$n [0], -$n [1] ];

    $side   = 9 + padDiagramWidth ( $text ) / 2 * abs ( $u [1] );
    $x      = $to [0] - $u [0] * 20 + $n [0] * $side;
    $y      = $to [1] - $u [1] * 20 + $n [1] * $side + 4;

    return '<text class="pd-end" x="' . $X ( $x ) . '" y="' . $Y ( $y ) . '" text-anchor="middle">' . padChartAttr ( $text ) . '</text>';

  }

  // The rules of the marks beyond the arrow, for the kinds that draw them inside their own
  // <style>: the hollow ones filled with the background so the line stops at their outline,
  // the feet and the rings in the colour of the edges.

  function padDiagramMarkStyle () {

    return '.pad-chart-diagram .pd-hollow{fill:var(--pad-chart-surface);stroke:var(--pad-diagram-edge);stroke-width:1.5;stroke-linejoin:round}'
         . '.pad-chart-diagram .pd-foot{fill:none;stroke:var(--pad-diagram-edge);stroke-width:1.5}'
         . '.pad-chart-diagram .pd-ring{fill:var(--pad-chart-surface);stroke:var(--pad-diagram-edge);stroke-width:1.5}'
         . '.pad-chart-diagram .pd-end{font-size:11px}';

  }

  // The ranks of the layout: the edges that close a cycle turned, then the longest path
  // from a node without edges coming in - the rank, the turned edges and the edges as laid.
  // Worked out once, before the layout (padDiagramGraph): the gaps for the labels are
  // measured by these same ranks - where a relaxation of the layout's own pumped the ranks
  // of a chart with a cycle up, and the gap a label was given opened between other ranks
  // than the ones its edge crossed, the label lay over the nodes.

  function padDiagramRanking ( $nodes, $edges ) {

    // The ids as the text names them: PHP makes the key of a numeric id - 1 --> 2 --> 3 - an
    // integer, the edges hold it as text, and the ranks compared the two strictly: no root
    // matched, every node stayed in rank 0 and the chart was drawn flat.

    $ids  = array_map ( 'strval', array_keys ( $nodes ) );
    $next = array_fill_keys ( $ids, [] );

    foreach ( $edges as $e => list ( $a, $b ) )
      if ( $a !== $b )
        $next [$a] [] = [ $b, $e ];

    // The edges that close a cycle, turned.

    $state = $turned = [];

    foreach ( $ids as $root ) {

      if ( isset ( $state [$root] ) )
        continue;

      $stack          = [ [ $root, 0 ] ];
      $state [$root]  = 1;

      while ( $stack ) {

        list ( $id, $k ) = end ( $stack );

        if ( $k >= count ( $next [$id] ) ) {
          $state [$id] = 2;
          array_pop ( $stack );
          continue;
        }

        $stack [ count ( $stack ) - 1 ] [1]++;
        list ( $to, $e ) = $next [$id] [$k];

        if ( ( $state [$to] ?? 0 ) == 1 )
          $turned [$e] = TRUE;
        elseif ( ! isset ( $state [$to] ) ) {
          $state [$to] = 1;
          $stack [] = [ $to, 0 ];
        }

      }

    }

    // The ranks: the longest path from a node without edges coming in.

    $dag = $into = [];

    foreach ( $edges as $e => list ( $a, $b ) )
      if ( $a !== $b ) {
        $pair      = isset ( $turned [$e] ) ? [ $b, $a ] : [ $a, $b ];
        $dag [$e]  = $pair;
        $into [ $pair [1] ] = ( $into [ $pair [1] ] ?? 0 ) + 1;
      }

    $rank  = array_fill_keys ( $ids, 0 );
    $queue = $out = [];

    foreach ( $ids as $id )
      if ( ! isset ( $into [$id] ) )
        $queue [] = $id;

    // A node's edges out, in the order of the text, walked when the node's turn comes: every
    // edge was walked for every node, and a chain of 8000 steps took three seconds here.

    foreach ( $dag as list ( $a, $b ) )
      $out [$a] [] = $b;

    for ( $q = 0; $q < count ( $queue ); $q++ )
      foreach ( $out [ $queue [$q] ] ?? [] as $b ) {
        $rank [$b] = max ( $rank [$b], $rank [ $queue [$q] ] + 1 );
        if ( --$into [$b] == 0 )
          $queue [] = $b;
      }

    return [ $rank, $turned, $dag ];

  }

  // The point a part of the way along a line of points - half way by default.

  function padDiagramMiddle ( $points, $part = 0.5 ) {

    $total = 0;
    for ( $i = 1; $i < count ( $points ); $i++ )
      $total += hypot ( $points [$i] [0] - $points [$i - 1] [0], $points [$i] [1] - $points [$i - 1] [1] );

    $left = $total * $part;

    for ( $i = 1; $i < count ( $points ); $i++ ) {

      $length = hypot ( $points [$i] [0] - $points [$i - 1] [0], $points [$i] [1] - $points [$i - 1] [1] );

      if ( $length >= $left and $length > 0 ) {
        $t = $left / $length;
        return [ $points [$i - 1] [0] + $t * ( $points [$i] [0] - $points [$i - 1] [0] ), $points [$i - 1] [1] + $t * ( $points [$i] [1] - $points [$i - 1] [1] ) ];
      }

      $left -= $length;

    }

    return $points [0];

  }

  // The end of a line moved back by $by toward the point before: the arrowhead covers it.

  function padDiagramShorten ( $from, $to, $by ) {

    $length = hypot ( $to [0] - $from [0], $to [1] - $from [1] );

    if ( $length <= $by )
      return $to;

    return [ $to [0] - ( $to [0] - $from [0] ) * $by / $length, $to [1] - ( $to [1] - $from [1] ) * $by / $length ];

  }

  // An arrowhead at $to, pointing away from $from.

  function padDiagramHead ( $from, $to, $X, $Y, $style = 'solid' ) {

    $length = hypot ( $to [0] - $from [0], $to [1] - $from [1] ) ?: 1;
    $ux     = ( $to [0] - $from [0] ) / $length;
    $uy     = ( $to [1] - $from [1] ) / $length;
    $long   = ( $style == 'thick' ) ? 11 : 9;
    $half   = ( $style == 'thick' ) ? 6 : 4.5;
    $bx     = $to [0] - $ux * $long;
    $by     = $to [1] - $uy * $long;

    return '<path class="pd-head" d="M' . $X ( $to [0] ) . ',' . $Y ( $to [1] )
         . 'L' . $X ( $bx - $uy * $half ) . ',' . $Y ( $by + $ux * $half )
         . 'L' . $X ( $bx + $uy * $half ) . ',' . $Y ( $by - $ux * $half ) . 'Z"/>';

  }

  // The d of an edge: orthogonal with rounded turns, straight, or curved - a cubic per
  // step that leaves and arrives along the main axis.

  function padDiagramPath ( $points, $form, $vertical, $X, $Y ) {

    $d = 'M' . $X ( $points [0] [0] ) . ',' . $Y ( $points [0] [1] );
    $n = count ( $points );

    if ( $form == 'straight' ) {
      for ( $i = 1; $i < $n; $i++ )
        $d .= 'L' . $X ( $points [$i] [0] ) . ',' . $Y ( $points [$i] [1] );
      return $d;
    }

    if ( $form == 'curved' ) {
      for ( $i = 1; $i < $n; $i++ ) {
        list ( $ax, $ay ) = $points [$i - 1];
        list ( $bx, $by ) = $points [$i];
        if ( $vertical )
          $d .= 'C' . $X ( $ax ) . ',' . $Y ( ( $ay + $by ) / 2 ) . ' ' . $X ( $bx ) . ',' . $Y ( ( $ay + $by ) / 2 ) . ' ' . $X ( $bx ) . ',' . $Y ( $by );
        else
          $d .= 'C' . $X ( ( $ax + $bx ) / 2 ) . ',' . $Y ( $ay ) . ' ' . $X ( ( $ax + $bx ) / 2 ) . ',' . $Y ( $by ) . ' ' . $X ( $bx ) . ',' . $Y ( $by );
      }
      return $d;
    }

    for ( $i = 1; $i < $n; $i++ ) {

      if ( $i == $n - 1 ) {
        $d .= 'L' . $X ( $points [$i] [0] ) . ',' . $Y ( $points [$i] [1] );
        break;
      }

      list ( $px, $py ) = $points [$i - 1];
      list ( $cx, $cy ) = $points [$i];
      list ( $nx, $ny ) = $points [$i + 1];

      $in  = hypot ( $cx - $px, $cy - $py );
      $out = hypot ( $nx - $cx, $ny - $cy );
      $r   = min ( 8, $in / 2, $out / 2 );

      if ( $r < 0.5 ) {
        $d .= 'L' . $X ( $cx ) . ',' . $Y ( $cy );
        continue;
      }

      $d .= 'L' . $X ( $cx - ( $cx - $px ) / $in * $r ) . ',' . $Y ( $cy - ( $cy - $py ) / $in * $r )
          . 'Q' . $X ( $cx ) . ',' . $Y ( $cy ) . ' ' . $X ( $cx + ( $nx - $cx ) / $out * $r ) . ',' . $Y ( $cy + ( $ny - $cy ) / $out * $r );

    }

    return $d;

  }

?>
