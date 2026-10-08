<?php

  // Diagrams from a few lines of text - the {diagram} tag. A flowchart in the Mermaid way of
  // writing it, laid out on the server and written as inline SVG: no JavaScript, so it works
  // in print and in an e-mail, and the same text always draws the same picture.
  //
  //   {diagram}                          {diagram type='sequence'}
  //     A[Order placed] --> B{Paid?}       Shop ->> Bank: Charge 20 EUR
  //     B -->|yes| C((Ship))               Bank -->> Shop: Approved
  //     B -.->|no| D[Remind]             {/diagram}
  //   {/diagram}
  //
  // padDiagramParse      the lines of a flowchart: nodes, their shapes and the edges
  // padDiagramLayout     the layers: ranks by the longest path, the order in each rank by
  //                      barycentres, the positions across by isotonic regression
  // padDiagramFlow       the SVG of a flowchart
  // padDiagramSequence   the SVG of a sequence diagram: lifelines and the messages between
  //
  // The picture opens as a {chart} does (padChartOpen): role="img", a <title> and a <desc>
  // that says every edge or message in words. The colours are custom properties on
  // .pad-chart-diagram - --pad-diagram-node, --pad-diagram-border, --pad-diagram-decision,
  // --pad-diagram-decision-border, --pad-diagram-round, --pad-diagram-round-border,
  // --pad-diagram-edge and --pad-diagram-note - with light-dark() defaults.

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

      $line = rtrim ( trim ( preg_replace ( '/%%.*$/', '', $line ) ), ';' );

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

        $label = trim ( ( $match ['label'] ?? '' ) !== '' ? $match ['label'] : ( $match ['text'] ?? '' ), " \"" );
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

      if ( ! preg_match ( '/\G\s*([\p{L}\p{N}_]+)/u', $line, $match, 0, $at ) )
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

  // A text in lines of at most $max characters, broken at spaces; <br> breaks it too.

  function padDiagramWrap ( $text, $max = 24 ) {

    $lines = [];

    foreach ( preg_split ( '/<br\s*\/?>|\\\\n/i', (string) $text ) as $part ) {

      $line = '';

      foreach ( preg_split ( '/\s+/u', trim ( $part ) ) as $word )
        if ( $line === '' )
          $line = $word;
        elseif ( mb_strlen ( "$line $word" ) <= $max )
          $line .= " $word";
        else {
          $lines [] = $line;
          $line     = $word;
        }

      $lines [] = $line;

    }

    return $lines;

  }

  // The width of a text at the diagram's 12px, about.

  function padDiagramWidth ( $text ) {

    return mb_strlen ( (string) $text ) * 7;

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

    return '<style>'
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

    if ( $shape == 'circle' )
      return sqrt ( max ( 0, $main * $main - $off * $off ) );

    if ( $shape == 'diamond' )
      return $main * ( 1 - $off / $cross );

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
  // the same for the same text, every time.

  function padDiagramLayout ( &$nodes, $edges, $vertical, $gaps ) {

    $ids  = array_keys ( $nodes );
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
    $queue = [];

    foreach ( $ids as $id )
      if ( ! isset ( $into [$id] ) )
        $queue [] = $id;

    for ( $q = 0; $q < count ( $queue ); $q++ )
      foreach ( $dag as list ( $a, $b ) )
        if ( $a === $queue [$q] ) {
          $rank [$b] = max ( $rank [$b], $rank [$a] + 1 );
          if ( --$into [$b] == 0 )
            $queue [] = $b;
        }

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

    // The main axis: each rank as deep as its deepest node, the gaps between as wide as
    // their labels ask.

    $main = [];
    $at   = 0;

    foreach ( $ranks as $r => $list ) {

      $depth = 0;
      foreach ( $list as $id )
        $depth = max ( $depth, $vertical ? $nodes [$id] ['h'] : $nodes [$id] ['w'] );

      $main [$r] = [ $at, $depth ];
      $at       += $depth + ( $gaps [$r] ?? 44 );

    }

    foreach ( $ranks as $r => $list )
      foreach ( $list as $id )
        $nodes [$id] ['main'] = $main [$r] [0] + $main [$r] [1] / 2;

    // The cross axis: packed, then each rank moved toward its neighbours, down and up.

    $size  = fn ( $id ) => $vertical ? $nodes [$id] ['w'] : $nodes [$id] ['h'];
    $space = fn ( $a, $b ) => ( $size ( $a ) + $size ( $b ) ) / 2 + ( ( $nodes [$a] ['shape'] == 'dummy' or $nodes [$b] ['shape'] == 'dummy' ) ? 14 : 30 );
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

    $vertical = in_array ( $direction, [ 'TB', 'BT' ] );
    $desc     = [];

    foreach ( $nodes as $id => list ( $text, $shape ) ) {
      $lines        = padDiagramWrap ( $text );
      list ( $w, $h ) = padDiagramSize ( $lines, $shape );
      $nodes [$id]  = [ 'text' => $text, 'shape' => $shape, 'lines' => $lines, 'w' => $w, 'h' => $h ];
    }

    foreach ( $edges as list ( $a, $b, $label ) )
      $desc [] = $nodes [$a] ['text'] . ' → ' . $nodes [$b] ['text'] . ( $label !== '' ? " ($label)" : '' );

    if ( ! $edges )
      foreach ( $nodes as $node )
        $desc [] = $node ['text'];

    // A gap as wide as the labels of the edges that leave the rank before it: in LR the
    // text lies along the edge.

    $rankOf = padDiagramRanksOnly ( $nodes, $edges );
    $gaps   = [];

    foreach ( $edges as list ( $a, $b, $label ) )
      if ( $label !== '' and $a !== $b ) {
        $r    = min ( $rankOf [$a], $rankOf [$b] );
        $need = $vertical ? 60 : padDiagramWidth ( $label ) + 36;
        $gaps [$r] = max ( $gaps [$r] ?? 44, $need );
      }

    list ( $chains, $turned ) = padDiagramLayout ( $nodes, $edges, $vertical, $gaps );

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
        $step  = ( $k > 1 ) ? min ( 14, $width * 0.6 / ( $k - 1 ) ) : 0;

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
    // does not cut through another edge's way down.

    $bends = $gapSegments = [];

    foreach ( $chains as $e => $chain )
      for ( $i = 1; $i < count ( $chain ); $i++ ) {
        list ( $p, $q ) = $segment ( $e, $i );
        if ( abs ( $p [0] - $q [0] ) > 1 )
          $gapSegments [ round ( $p [1] ) . ':' . round ( $q [1] ) ] [] = [ $e, $i, $p, $q ];
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

    $loops = [];

    foreach ( $edges as $e => list ( $a, $b, $label ) )
      if ( $a === $b ) {
        $k          = $loops [$a] = ( $loops [$a] ?? -1 ) + 1;
        $n          = $nodes [$a];
        $boxes []   = [ $n ['x'], $n ['y'] - 28 - 6 * $k, $n ['x'] + $n ['w'] / 2 + 30 + 22 * $k + ( $label !== '' ? padDiagramWidth ( $label ) + 8 : 0 ), $n ['y'] + 28 + 6 * $k ];
      }

    $loops = [];

    foreach ( $paths as $points )
      foreach ( $points as list ( $x, $y ) )
        $boxes [] = [ $x, $y, $x, $y ];

    // The labels sit half way along their edge, on a plate of the background.

    $labels = [];

    foreach ( $edges as $e => list ( $a, $b, $label ) )
      if ( $label !== '' and isset ( $paths [$e] ) ) {

        $w = padDiagramWidth ( $label ) + 8;

        // Half way, else a little before or after where another label stands there.

        $clash = function ( $x, $y ) use ( &$labels, $w ) {
          foreach ( $labels as list ( $ox, $oy, $ow ) )
            if ( abs ( $ox - $x ) < ( $ow + $w ) / 2 + 2 and abs ( $oy - $y ) < 20 )
              return [ $ox, $oy, $ow ];
          return NULL;
        };

        foreach ( [ 0.5, 0.35, 0.65, 0.22, 0.78, 0.5 ] as $part ) {
          list ( $x, $y ) = padDiagramMiddle ( $paths [$e], $part );
          if ( ! ( $other = $clash ( $x, $y ) ) )
            break;
        }

        // Nowhere along its edge: beside the label in the way, across the flow.

        if ( $other ) {
          if ( $vertical )
            $x = $other [0] + ( $x >= $other [0] ? 1 : -1 ) * ( ( $other [2] + $w ) / 2 + 4 );
          else
            $y = $other [1] + ( $y >= $other [1] ? 1 : -1 ) * 21;
        }

        $labels [$e] = [ $x, $y, $w ];
        $boxes []    = [ $x - $w / 2, $y - 9, $x + $w / 2, $y + 9 ];

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

    $svg .= padDiagramStyle ();

    // The edges first, the nodes over their ends.

    foreach ( $edges as $e => list ( $a, $b, $label, $style, $arrow ) ) {

      $class = 'pd-edge' . ( $style != 'solid' ? " pd-$style" : '' );
      $tip   = '<title>' . padChartAttr ( $desc [$e] ) . '</title>';

      if ( $a === $b ) {

        // A loop on the right of its node, a wider one for every further loop there.

        $k  = $loops [$a] = ( $loops [$a] ?? -1 ) + 1;
        $n  = $nodes [$a];
        $x  = $n ['x'] + $n ['w'] / 2 - ( $n ['shape'] == 'diamond' ? $n ['w'] / 4 : 0 );
        $y  = $n ['y'];
        $r  = 34 + 22 * $k;
        $d  = 'M' . $X ( $x ) . ',' . $Y ( $y - 8 ) . 'C' . $X ( $x + $r ) . ',' . $Y ( $y - 28 - 6 * $k ) . ' ' . $X ( $x + $r ) . ',' . $Y ( $y + 28 + 6 * $k ) . ' ' . $X ( $x + 6 ) . ',' . $Y ( $y + 10 );
        $svg .= "<g class=\"pc-mark\">$tip<path class=\"$class\" d=\"$d\"/>"
              . ( $arrow ? padDiagramHead ( [ $x + 12, $y + 14 ], [ $x, $y + 8 ], $X, $Y, $style ) : '' ) . '</g>';

        if ( $label !== '' )
          $svg .= '<text x="' . $X ( $x + $r * 0.75 + 4 ) . '" y="' . $Y ( $y + 4 ) . '">' . padChartAttr ( $label ) . '</text>';

        continue;

      }

      $points = $paths [$e];

      if ( $arrow )
        $points [ count ( $points ) - 1 ] = padDiagramShorten ( $points [ count ( $points ) - 2 ], end ( $points ), $style == 'thick' ? 9 : 7 );

      $svg .= "<g class=\"pc-mark\">$tip<path class=\"$class\" d=\"" . padDiagramPath ( $points, $edgeForm, $vertical, $X, $Y ) . '"/>'
            . ( $arrow ? padDiagramHead ( $paths [$e] [ count ( $paths [$e] ) - 2 ], end ( $paths [$e] ), $X, $Y, $style ) : '' ) . '</g>';

    }

    foreach ( $labels as $e => list ( $x, $y, $w ) )
      $svg .= '<rect class="pd-plate" x="' . $X ( $x - $w / 2 ) . '" y="' . $Y ( $y - 9 ) . '" width="' . padChartXY ( $w ) . '" height="18" rx="3"/>'
            . '<text x="' . $X ( $x ) . '" y="' . $Y ( $y + 4 ) . '" text-anchor="middle">' . padChartAttr ( $edges [$e] [2] ) . '</text>';

    foreach ( $nodes as $node ) {

      if ( $node ['shape'] == 'dummy' )
        continue;

      list ( $x, $y, $w, $h ) = [ $node ['x'], $node ['y'], $node ['w'], $node ['h'] ];

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
      } else
        $svg .= '<rect class="pd-node" x="' . $X ( $x - $w / 2 ) . '" y="' . $Y ( $y - $h / 2 ) . '" width="' . padChartXY ( $w ) . '" height="' . padChartXY ( $h )
              . '" rx="' . ( $node ['shape'] == 'round' ? padChartXY ( $h / 2 ) : 4 ) . '"/>';

      $svg .= padDiagramText ( $node ['lines'], $x - $minX, $y - $minY + ( $node ['shape'] == 'database' ? 5 : 0 ) );

    }

    return "$svg</svg>";

  }

  // The ranks alone, before the layout: what the gaps for the labels are measured by.

  function padDiagramRanksOnly ( $nodes, $edges ) {

    $rank = array_fill_keys ( array_keys ( $nodes ), 0 );

    for ( $round = 0; $round < count ( $nodes ); $round++ ) {
      $moved = FALSE;
      foreach ( $edges as list ( $a, $b ) )
        if ( $a !== $b and $rank [$b] < $rank [$a] + 1 and $rank [$a] + 1 < count ( $nodes ) ) {
          $rank [$b] = $rank [$a] + 1;
          $moved     = TRUE;
        }
      if ( ! $moved )
        break;
    }

    return $rank;

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

  // A sequence diagram: participants side by side, a dashed lifeline under each, and the
  // messages from top to bottom.
  //
  //   participant A as Alice      actor U as User        A ->> B: Hello     (solid, arrow)
  //   B -->> A: Hi                (dashed, arrow)        A -> B / A --> B   (no arrowhead)
  //   A -x B: Lost                (a cross)              A -) B: Later      (an open arrow)
  //   Note over A,B: text         Note left of A: text   Note right of A: text
  //   loop Every minute / alt Paid / else Unpaid / opt Maybe / par / and ... end
  //   autonumber                  (a number before each message)

  function padDiagramSequence ( $source, $title ) {

    global $padCheckSyntax;

    $people = $events = $bad = [];
    $number = FALSE;
    $id     = '[\p{L}\p{N}_]+';

    $person = function ( $name, $show = NULL, $actor = FALSE ) use ( &$people ) {
      if ( ! isset ( $people [$name] ) )
        $people [$name] = [ $show ?? $name, $actor ];
      elseif ( $show !== NULL )
        $people [$name] = [ $show, $actor ];
    };

    foreach ( preg_split ( '/\r?\n/', $source ) as $at => $line ) {

      $line = trim ( preg_replace ( '/%%.*$/', '', $line ) );

      if ( $line === '' or strcasecmp ( $line, 'sequenceDiagram' ) == 0 )
        continue;

      if ( strcasecmp ( $line, 'autonumber' ) == 0 )
        $number = TRUE;
      elseif ( preg_match ( "/^(participant|actor)\s+($id)(?:\s+as\s+(.+))?$/iu", $line, $m ) )
        $person ( $m [2], isset ( $m [3] ) ? trim ( $m [3] ) : NULL, strtolower ( $m [1] ) == 'actor' );
      elseif ( preg_match ( "/^($id)\s*(-->>|->>|-->|->|--x|-x|--\)|-\))\s*($id)\s*(?::\s*(.*))?$/u", $line, $m ) ) {
        $person ( $m [1] );
        $person ( $m [3] );
        $head      = str_ends_with ( $m [2], '>>' ) ? 'arrow' : ( str_ends_with ( $m [2], 'x' ) ? 'cross' : ( str_ends_with ( $m [2], ')' ) ? 'open' : 'none' ) );
        $events [] = [ 'message', $m [1], $m [3], trim ( $m [4] ?? '' ), str_starts_with ( $m [2], '--' ), $head ];
      } elseif ( preg_match ( "/^note\s+(over|left of|right of)\s+($id)(?:\s*,\s*($id))?\s*:\s*(.*)$/iu", $line, $m ) ) {
        $person ( $m [2] );
        if ( ( $m [3] ?? '' ) !== '' )
          $person ( $m [3] );
        $events [] = [ 'note', strtolower ( $m [1] ), $m [2], ( $m [3] ?? '' ) !== '' ? $m [3] : $m [2], trim ( $m [4] ) ];
      } elseif ( preg_match ( '/^(loop|alt|opt|par|critical|break)\b\s*(.*)$/i', $line, $m ) )
        $events [] = [ 'open', strtolower ( $m [1] ), trim ( $m [2] ) ];
      elseif ( preg_match ( '/^(else|and|option)\b\s*(.*)$/i', $line, $m ) )
        $events [] = [ 'else', trim ( $m [2] ) ];
      elseif ( strcasecmp ( $line, 'end' ) == 0 )
        $events [] = [ 'end' ];
      else
        $bad [] = [ $at + 1, $line ];

    }

    if ( $bad and $padCheckSyntax )
      padError ( 'the sequence diagram cannot read line ' . $bad [0] [0] . ": '" . padMakeSafe ( $bad [0] [1], 60 ) . "' - write A ->> B: text, B -->> A: text, Note over A: text, loop ... end" );

    if ( ! $people )
      return '';

    $names = array_keys ( $people );
    $col   = array_flip ( $names );
    $n     = count ( $names );

    // The columns: wide enough for the boxes and for every message between them.

    $boxW = [];
    foreach ( $names as $i => $name )
      $boxW [$i] = max ( 80, padDiagramWidth ( $people [$name] [0] ) + 28 );

    $gap = [];
    for ( $i = 0; $i < $n - 1; $i++ )
      $gap [$i] = max ( 60, ( $boxW [$i] + $boxW [$i + 1] ) / 2 + 30 );

    $wide = [];

    foreach ( $events as $event )
      if ( $event [0] == 'message' and $event [1] !== $event [2] ) {
        $a       = min ( $col [ $event [1] ], $col [ $event [2] ] );
        $b       = max ( $col [ $event [1] ], $col [ $event [2] ] );
        $wide [] = [ $a, $b, padDiagramWidth ( $event [3] ) + ( $number ? 60 : 40 ) ];
      } elseif ( $event [0] == 'note' and $event [1] == 'over' and $event [2] !== $event [3] ) {
        $a       = min ( $col [ $event [2] ], $col [ $event [3] ] );
        $b       = max ( $col [ $event [2] ], $col [ $event [3] ] );
        $wide [] = [ $a, $b, min ( 220, padDiagramWidth ( $event [4] ) ) ];
      }

    usort ( $wide, fn ( $p, $q ) => [ $p [1] - $p [0], $p [0] ] <=> [ $q [1] - $q [0], $q [0] ] );

    foreach ( $wide as list ( $a, $b, $need ) ) {
      $have = 0;
      for ( $i = $a; $i < $b; $i++ )
        $have += $gap [$i];
      if ( $need > $have )
        for ( $i = $a; $i < $b; $i++ )
          $gap [$i] += ( $need - $have ) / ( $b - $a );
    }

    $left  = 12 + $boxW [0] / 2;
    $x     = [ $left ];
    for ( $i = 1; $i < $n; $i++ )
      $x [$i] = $x [$i - 1] + $gap [$i - 1];

    // Notes and messages to oneself reach right of the last column.

    $right = $x [$n - 1] + $boxW [$n - 1] / 2;

    foreach ( $events as $event )
      if ( $event [0] == 'message' and $event [1] === $event [2] )
        $right = max ( $right, $x [ $col [ $event [1] ] ] + 44 + padDiagramWidth ( $event [3] ) );
      elseif ( $event [0] == 'note' and $event [1] == 'right of' )
        $right = max ( $right, $x [ $col [ $event [2] ] ] + 18 + min ( 220, padDiagramWidth ( $event [4] ) + 16 ) );

    $shift = 0;

    foreach ( $events as $event )
      if ( $event [0] == 'note' and $event [1] == 'left of' )
        $shift = max ( $shift, min ( 220, padDiagramWidth ( $event [4] ) + 16 ) + 18 - $x [ $col [ $event [2] ] ] + 12 );

    foreach ( $x as $i => $value )
      $x [$i] = $value + $shift;

    $right += $shift;

    // The rows, top to bottom; the frames of loop, alt and the rest as they close.

    $boxH   = 34;
    $y      = 12 + $boxH + 16;
    $rows   = $frames = $stack = $desc = [];
    $count  = 0;

    foreach ( $events as $event ) {

      switch ( $event [0] ) {

        case 'message':

          $self     = ( $event [1] === $event [2] );
          $y       += 20;
          $rows []  = [ 'message', $y, $event, ++$count ];
          $y       += $self ? 34 : 14;
          $desc []  = $people [ $event [1] ] [0] . ' to ' . $people [ $event [2] ] [0] . ( $event [3] !== '' ? ': ' . $event [3] : '' );

          foreach ( $stack as $k => $frame ) {
            $stack [$k] [2] = min ( $frame [2], $col [ $event [1] ], $col [ $event [2] ] );
            $stack [$k] [3] = max ( $frame [3], $col [ $event [1] ], $col [ $event [2] ] );
          }

          break;

        case 'note':

          $span     = ( $event [1] == 'over' ) ? abs ( $x [ $col [ $event [2] ] ] - $x [ $col [ $event [3] ] ] ) + 24 : 0;
          $lines    = padDiagramWrap ( $event [4], max ( 30, (int) floor ( $span / 7 ) ) );
          $h        = 16 * count ( $lines ) + 10;
          $y       += 8;
          $rows []  = [ 'note', $y, $event, $lines, $h ];
          $y       += $h;
          $desc []  = 'Note: ' . $event [4];

          foreach ( $stack as $k => $frame ) {
            $stack [$k] [2] = min ( $frame [2], $col [ $event [2] ], $col [ $event [3] ] );
            $stack [$k] [3] = max ( $frame [3], $col [ $event [2] ], $col [ $event [3] ] );
          }

          break;

        case 'open':

          $y        += 10;
          $stack []  = [ $event [1], $y, PHP_INT_MAX, -1, [ [ $y, $event [2] ] ] ];
          $y        += 24;
          $desc []   = ucfirst ( $event [1] ) . ( $event [2] !== '' ? ': ' . $event [2] : '' );
          break;

        case 'else':

          if ( $stack ) {
            $y += 8;
            $stack [ count ( $stack ) - 1 ] [4] [] = [ $y, $event [1] ];
            $y += 22;
            $desc [] = 'Else' . ( $event [1] !== '' ? ': ' . $event [1] : '' );
          }

          break;

        case 'end':

          if ( $stack ) {
            $y         += 10;
            $frame      = array_pop ( $stack );
            $frames []  = [ $frame [0], $frame [1], $y, $frame [2], $frame [3], $frame [4], count ( $stack ) ];
            foreach ( $stack as $k => $outer ) {
              $stack [$k] [2] = min ( $outer [2], $frame [2] );
              $stack [$k] [3] = max ( $outer [3], $frame [3] );
            }
          }

          break;

      }

    }

    while ( $stack ) {
      $frame     = array_pop ( $stack );
      $frames [] = [ $frame [0], $frame [1], $y + 10, $frame [2], $frame [3], $frame [4], count ( $stack ) ];
      $y        += 10;
    }

    $bottom = $y + 20;
    $width  = (int) ceil ( $right + 12 );
    $height = (int) ceil ( $bottom + $boxH + 12 );

    list ( $svgId, $svg ) = padChartOpen ( 'diagram', [ $people, $events, $number ], $desc, $title, $width, $height, FALSE );

    $svg .= padDiagramStyle ();
    $X    = fn ( $v ) => padChartXY ( $v );

    // The participants at the top and the foot, the lifelines between.

    foreach ( $names as $i => $name ) {

      $w    = $boxW [$i];
      $rx   = $people [$name] [1] ? padChartXY ( $boxH / 2 ) : 4;
      $svg .= '<line class="pd-life" x1="' . $X ( $x [$i] ) . '" x2="' . $X ( $x [$i] ) . '" y1="' . ( 12 + $boxH ) . '" y2="' . $X ( $bottom ) . '"/>';

      foreach ( [ 12, $bottom ] as $top )
        $svg .= '<rect class="pd-node' . ( $people [$name] [1] ? ' pd-circle' : '' ) . '" x="' . $X ( $x [$i] - $w / 2 ) . '" y="' . $X ( $top ) . '" width="' . $X ( $w ) . "\" height=\"$boxH\" rx=\"$rx\"/>"
              . padDiagramText ( [ $people [$name] [0] ], $x [$i], $top + $boxH / 2 );

    }

    // The frames, the outer ones first: a box round the columns their messages touch, a
    // tab with the kind, the condition beside it and a dashed line before each else.

    usort ( $frames, fn ( $p, $q ) => [ $p [6], $p [1] ] <=> [ $q [6], $q [1] ] );

    foreach ( $frames as list ( $kind, $top, $end, $a, $b, $parts, $depth ) ) {

      if ( $b < 0 ) {
        $a = 0;
        $b = $n - 1;
      }

      $inset = 8 * $depth;
      $x0    = max ( 4, $x [$a] - max ( 50, $boxW [$a] / 2 + 6 ) + $inset );
      $x1    = min ( $width - 4, $x [$b] + max ( 50, $boxW [$b] / 2 + 6 ) - $inset );
      $tabW  = padDiagramWidth ( $kind ) + 14;

      $svg .= '<rect class="pd-frame" x="' . $X ( $x0 ) . '" y="' . $X ( $top ) . '" width="' . $X ( $x1 - $x0 ) . '" height="' . $X ( $end - $top ) . '"/>'
            . '<path class="pd-tab" d="M' . $X ( $x0 ) . ',' . $X ( $top ) . 'h' . $X ( $tabW ) . 'v12l-6,6H' . $X ( $x0 ) . 'Z"/>'
            . '<text class="pd-small" x="' . $X ( $x0 + 6 ) . '" y="' . $X ( $top + 13 ) . '">' . padChartAttr ( $kind ) . '</text>';

      foreach ( $parts as $k => list ( $at, $text ) ) {
        if ( $k )
          $svg .= '<line class="pd-frame pd-dashed" x1="' . $X ( $x0 ) . '" x2="' . $X ( $x1 ) . '" y1="' . $X ( $at ) . '" y2="' . $X ( $at ) . '"/>';
        if ( $text !== '' )
          $svg .= '<text x="' . $X ( $k ? $x0 + 8 : $x0 + $tabW + 8 ) . '" y="' . $X ( $at + 14 ) . '">[' . padChartAttr ( $text ) . ']</text>';
      }

    }

    // The messages and the notes.

    foreach ( $rows as $row ) {

      if ( $row [0] == 'note' ) {

        list ( , $top, $event, $lines, $h ) = $row;

        $textW = 0;
        foreach ( $lines as $line )
          $textW = max ( $textW, padDiagramWidth ( $line ) );

        $a = $x [ $col [ $event [2] ] ];
        $b = $x [ $col [ $event [3] ] ];

        if ( $event [1] == 'over' ) {
          $x0 = min ( $a, $b ) - 20;
          $x1 = max ( $a, $b ) + 20;
          if ( $x1 - $x0 < $textW + 16 ) {
            $mid = ( $x0 + $x1 ) / 2;
            $x0  = $mid - ( $textW + 16 ) / 2;
            $x1  = $mid + ( $textW + 16 ) / 2;
          }
        } elseif ( $event [1] == 'left of' ) {
          $x1 = $a - 18;
          $x0 = $x1 - $textW - 16;
        } else {
          $x0 = $a + 18;
          $x1 = $x0 + $textW + 16;
        }

        $svg .= '<g><title>' . padChartAttr ( 'Note: ' . $event [4] ) . '</title>'
              . '<rect class="pd-note" x="' . $X ( $x0 ) . '" y="' . $X ( $top ) . '" width="' . $X ( $x1 - $x0 ) . '" height="' . $X ( $h ) . '" rx="2"/>'
              . padDiagramText ( $lines, ( $x0 + $x1 ) / 2, $top + $h / 2 ) . '</g>';

        continue;

      }

      list ( , $at, list ( , $from, $to, $text, $dashed, $head ), $nth ) = $row;

      $a     = $x [ $col [$from] ];
      $b     = $x [ $col [$to] ];
      $class = 'pd-edge' . ( $dashed ? ' pd-dashed' : '' );
      $tip   = '<title>' . padChartAttr ( $people [$from] [0] . ' → ' . $people [$to] [0] . ( $text !== '' ? ": $text" : '' ) ) . '</title>';
      $label = ( $number ? "$nth. " : '' ) . $text;

      if ( $from === $to ) {

        $d    = 'M' . $X ( $a ) . ',' . $X ( $at ) . 'h30v22h' . ( $head == 'arrow' ? '-24' : '-30' );
        $svg .= "<g class=\"pc-mark\">$tip<path class=\"$class\" d=\"$d\"/>"
              . padDiagramEnd ( [ $a + 30, $at + 22 ], [ $a, $at + 22 ], $head, $X, $X ) . '</g>';

        if ( $label !== '' )
          $svg .= '<text x="' . $X ( $a + 36 ) . '" y="' . $X ( $at + 15 ) . '">' . padChartAttr ( $label ) . '</text>';

        continue;

      }

      $end  = ( $head == 'arrow' or $head == 'open' ) ? padDiagramShorten ( [ $a, $at ], [ $b, $at ], $head == 'arrow' ? 7 : 1 ) : [ $b, $at ];
      $svg .= "<g class=\"pc-mark\">$tip<path class=\"$class\" d=\"M" . $X ( $a ) . ',' . $X ( $at ) . 'H' . $X ( $end [0] ) . '"/>'
            . padDiagramEnd ( [ $a, $at ], [ $b, $at ], $head, $X, $X ) . '</g>';

      if ( $label !== '' )
        $svg .= '<rect class="pd-plate" x="' . $X ( ( $a + $b ) / 2 - padDiagramWidth ( $label ) / 2 - 3 ) . '" y="' . $X ( $at - 18 ) . '" width="' . $X ( padDiagramWidth ( $label ) + 6 ) . '" height="15" rx="2"/>'
              . '<text x="' . $X ( ( $a + $b ) / 2 ) . '" y="' . $X ( $at - 6 ) . '" text-anchor="middle">' . padChartAttr ( $label ) . '</text>';

    }

    return "$svg</svg>";

  }

  // The end of a message: a filled arrowhead, an open one (two strokes), a cross or none.

  function padDiagramEnd ( $from, $to, $head, $X, $Y ) {

    if ( $head == 'arrow' )
      return padDiagramHead ( $from, $to, $X, $Y );

    if ( $head == 'none' )
      return '';

    $dir = ( $to [0] >= $from [0] ) ? 1 : -1;

    if ( $head == 'open' )
      return '<path class="pd-edge" d="M' . $X ( $to [0] - $dir * 9 ) . ',' . $Y ( $to [1] - 5 ) . 'L' . $X ( $to [0] ) . ',' . $Y ( $to [1] )
           . 'L' . $X ( $to [0] - $dir * 9 ) . ',' . $Y ( $to [1] + 5 ) . '"/>';

    return '<path class="pd-edge" d="M' . $X ( $to [0] - 5 ) . ',' . $Y ( $to [1] - 5 ) . 'l10,10m0,-10l-10,10"/>';

  }

?>
