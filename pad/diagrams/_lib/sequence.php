<?php

  // The sequence diagram - diagrams/types/sequence.php: participants side by side, a dashed
  // lifeline under each and the messages between them from top to bottom, with the notes
  // and the frames of loop, alt and the rest. The text, the colours and the arrowheads are
  // the diagram's own, diagrams/_lib/diagram.php.

  // A note of a sequence diagram: its lines, wrapped to the width between its participants,
  // and where it stands across - [ x0, x1 ] - over them, or beside one. The width of the
  // picture is made with the same two as the drawing.

  function padDiagramNoteLines ( $event, $x, $col ) {

    $span = ( $event [1] == 'over' ) ? abs ( $x [ $col [ $event [2] ] ] - $x [ $col [ $event [3] ] ] ) + 24 : 0;

    return padDiagramWrap ( $event [4], max ( 30, (int) floor ( $span / 7 ) ) );

  }

  function padDiagramNoteSpan ( $event, $lines, $a, $b ) {

    $textW = 0;

    foreach ( $lines as $line )
      $textW = max ( $textW, padDiagramWidth ( $line ) );

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

    return [ $x0, $x1 ];

  }

  // Where a frame of a sequence diagram stands across - [ x0, x1 ] - round the columns its
  // messages touch, an inner one inset, and wide enough for the tab and the condition of
  // each of its parts. $inner is how far the frames inside it reach - [ x0, x1 ] - and it
  // reaches past them: an inner frame widened for a long condition ran out through the
  // right side of the frame round it, and lay on its left side.

  function padDiagramFrameSpan ( $frame, $x, $boxW, $n, $inner = NULL ) {

    list ( $kind, , , $a, $b, $parts, $depth ) = $frame;

    if ( $b < 0 ) {
      $a = 0;
      $b = $n - 1;
    }

    // An inner frame is inset on the left, and as wide as its tab and its conditions need,
    // to the right - earlier a negative width, a rect no browser draws; the frames round it
    // widen with it. Inset past the picture's left edge, frames lie on each other there.

    $from = $x [$a] - max ( 50, $boxW [$a] / 2 + 6 );
    $to   = $x [$b] + max ( 50, $boxW [$b] / 2 + 6 );
    $tabW = padDiagramWidth ( $kind ) + 14;
    $need = $tabW + 16;

    foreach ( $parts as $k => list ( , $text ) )
      if ( $text !== '' )
        $need = max ( $need, ( $k ? 8 : $tabW + 8 ) + padDiagramWidth ( "[$text]" ) + 8 );

    $x0 = max ( 4, $from + 8 * $depth );
    $x1 = max ( $to - 8 * $depth, $x0 + $need );

    if ( $inner ) {
      $x0 = max ( 4, min ( $x0, $inner [0] - 8 ) );
      $x1 = max ( $x1, $inner [1] + 8 );
    }

    return [ $x0, $x1 ];

  }

  // The innermost open frame closes at $y: it goes to the frames with where it stands, and
  // the frame round it takes its columns and reaches past it. Frames close inner first, so
  // each knows what it holds when its own place is worked out.

  function padDiagramFrameClose ( &$stack, &$frames, $y, $x, $boxW, $n ) {

    $frame = array_pop ( $stack );
    $entry = [ $frame [0], $frame [1], $y, $frame [2], $frame [3], $frame [4], count ( $stack ) ];
    $span  = padDiagramFrameSpan ( $entry, $x, $boxW, $n, $frame [5] ?? NULL );

    $frames [] = array_merge ( $entry, [ $span ] );

    foreach ( $stack as $k => $outer ) {
      $stack [$k] [2] = min ( $outer [2], $frame [2] );
      $stack [$k] [3] = max ( $outer [3], $frame [3] );
    }

    if ( $stack ) {
      $k = count ( $stack ) - 1;
      $stack [$k] [5] = [ min ( $stack [$k] [5] [0] ?? INF, $span [0] ), max ( $stack [$k] [5] [1] ?? -INF, $span [1] ) ];
    }

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

    $people = $events = $bad = $frames = [];
    $number = FALSE;
    $id     = '[\p{L}\p{M}\p{N}_]+';

    // A declaration says what a participant is, with or without a name to show: actor U
    // after U had sent a message was a box, actor U as User a person.

    $person = function ( $name, $show = NULL, $actor = NULL ) use ( &$people ) {
      if ( ! isset ( $people [$name] ) )
        $people [$name] = [ $show ?? $name, (bool) $actor ];
      else {
        if ( $show !== NULL )
          $people [$name] [0] = $show;
        if ( $actor !== NULL )
          $people [$name] [1] = $actor;
      }
    };

    foreach ( preg_split ( '/\r?\n/', $source ) as $at => $line ) {

      // A comment is a line of its own, as in a flowchart: "Discount 50%% today" was
      // "Discount 50".

      $line = preg_match ( '/^\s*%%/', $line ) ? '' : trim ( $line );

      if ( $line === '' or strcasecmp ( $line, 'sequenceDiagram' ) == 0 )
        continue;

      // The messages after autonumber are numbered, from 1, as Mermaid numbers them from the
      // statement on: those before it were numbered too.

      if ( strcasecmp ( $line, 'autonumber' ) == 0 )
        $number = TRUE;
      elseif ( preg_match ( "/^(participant|actor)\s+($id)(?:\s+as\s+(.+))?$/iu", $line, $m ) )
        $person ( $m [2], isset ( $m [3] ) ? padDiagramFlat ( $m [3] ) : NULL, strtolower ( $m [1] ) == 'actor' );
      elseif ( preg_match ( "/^($id)\s*(-->>|->>|-->|->|--x|-x|--\)|-\))\s*($id)\s*(?::\s*(.*))?$/u", $line, $m ) ) {
        $person ( $m [1] );
        $person ( $m [3] );
        $head      = str_ends_with ( $m [2], '>>' ) ? 'arrow' : ( str_ends_with ( $m [2], 'x' ) ? 'cross' : ( str_ends_with ( $m [2], ')' ) ? 'open' : 'none' ) );
        $events [] = array_merge ( [ 'message', $m [1], $m [3], padDiagramFlat ( $m [4] ?? '' ), str_starts_with ( $m [2], '--' ), $head ], $number ? [ TRUE ] : [] );
      } elseif ( preg_match ( "/^note\s+(over|left of|right of)\s+($id)(?:\s*,\s*($id))?\s*:\s*(.*)$/iu", $line, $m ) ) {
        $person ( $m [2] );
        if ( ( $m [3] ?? '' ) !== '' )
          $person ( $m [3] );
        $events [] = [ 'note', strtolower ( $m [1] ), $m [2], ( $m [3] ?? '' ) !== '' ? $m [3] : $m [2], trim ( $m [4] ) ];
      } elseif ( preg_match ( '/^(loop|alt|opt|par|critical|break)\b\s*(.*)$/i', $line, $m ) ) {
        $events [] = [ 'open', strtolower ( $m [1] ), padDiagramFlat ( $m [2] ) ];
        $frames [] = [ $at + 1, strtolower ( $m [1] ) ];
      } elseif ( preg_match ( '/^(else|and|option)\b\s*(.*)$/i', $line, $m ) and $frames )
        $events [] = [ 'else', padDiagramFlat ( $m [2] ) ];
      elseif ( strcasecmp ( $line, 'end' ) == 0 and $frames ) {
        $events [] = [ 'end' ];
        array_pop ( $frames );
      } else
        $bad [] = [ $at + 1, $line ];

    }

    if ( $bad and $padCheckSyntax )
      padError ( 'the sequence diagram cannot read line ' . $bad [0] [0] . ": '" . padMakeSafe ( $bad [0] [1], 60 ) . "' - write A ->> B: text, B -->> A: text, Note over A: text, loop ... end" );

    // An else or an end outside any frame is a line it cannot read, and a frame never closed
    // is named, where both passed without a word: the strays vanished, and the open loop was
    // closed under the last message.

    if ( $frames and $padCheckSyntax )
      padError ( 'the sequence diagram has a ' . $frames [0] [1] . ' on line ' . $frames [0] [0] . ' that no end closes' );

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

    // A note takes the room it is drawn in (padDiagramNoteSpan): one over a single
    // participant, centred on its line, and a long word beside one were wider than the
    // picture and cut off.

    // A message to oneself takes the room of its label as it is drawn - its number in front,
    // where the room was the text's alone and "12. text" ran past the edge.

    $shift = 0;
    $nth   = 0;

    foreach ( $events as $event )
      if ( $event [0] == 'message' ) {
        $label = isset ( $event [6] ) ? ( ++$nth ) . '. ' . $event [3] : $event [3];
        if ( $event [1] === $event [2] )
          $right = max ( $right, $x [ $col [ $event [1] ] ] + 44 + padDiagramWidth ( $label ) );
      } elseif ( $event [0] == 'note' ) {
        list ( $x0, $x1 ) = padDiagramNoteSpan ( $event, padDiagramNoteLines ( $event, $x, $col ),
                                                 $x [ $col [ $event [2] ] ], $x [ $col [ $event [3] ] ] );
        $right = max ( $right, $x1 );
        $shift = max ( $shift, 12 - $x0 );
      }

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
          $rows []  = [ 'message', $y, $event, isset ( $event [6] ) ? ++$count : 0 ];
          $y       += $self ? 34 : 14;
          $desc []  = $people [ $event [1] ] [0] . ' to ' . $people [ $event [2] ] [0] . ( $event [3] !== '' ? ': ' . $event [3] : '' );

          foreach ( $stack as $k => $frame ) {
            $stack [$k] [2] = min ( $frame [2], $col [ $event [1] ], $col [ $event [2] ] );
            $stack [$k] [3] = max ( $frame [3], $col [ $event [1] ], $col [ $event [2] ] );
          }

          break;

        case 'note':

          $lines    = padDiagramNoteLines ( $event, $x, $col );
          $h        = 16 * count ( $lines ) + 10;
          $y       += 8;
          $rows []  = [ 'note', $y, $event, $lines, $h ];
          $y       += $h;
          $desc []  = 'Note: ' . padDiagramFlat ( $event [4] );

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
            $y += 10;
            padDiagramFrameClose ( $stack, $frames, $y, $x, $boxW, $n );
          }

          break;

      }

    }

    while ( $stack ) {
      padDiagramFrameClose ( $stack, $frames, $y + 10, $x, $boxW, $n );
      $y += 10;
    }

    $bottom = $y + 20;
    // A frame reaches past its columns as far as its conditions need, and the picture with
    // it: a long condition was written past the frame and the edge of the picture, cut off.

    foreach ( $frames as $frame )
      $right = max ( $right, $frame [7] [1] + 4 - 12 );

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

    foreach ( $frames as list ( $kind, $top, $end, $a, $b, $parts, $depth, $span ) ) {

      list ( $x0, $x1 ) = $span;

      $x1   = min ( $width - 4, $x1 );
      $tabW = padDiagramWidth ( $kind ) + 14;

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

        list ( $x0, $x1 ) = padDiagramNoteSpan ( $event, $lines, $x [ $col [ $event [2] ] ], $x [ $col [ $event [3] ] ] );

        $svg .= '<g><title>' . padChartAttr ( 'Note: ' . padDiagramFlat ( $event [4] ) ) . '</title>'
              . '<rect class="pd-note" x="' . $X ( $x0 ) . '" y="' . $X ( $top ) . '" width="' . $X ( $x1 - $x0 ) . '" height="' . $X ( $h ) . '" rx="2"/>'
              . padDiagramText ( $lines, ( $x0 + $x1 ) / 2, $top + $h / 2 ) . '</g>';

        continue;

      }

      list ( , $at, list ( , $from, $to, $text, $dashed, $head ), $nth ) = $row;

      $a     = $x [ $col [$from] ];
      $b     = $x [ $col [$to] ];
      $class = 'pd-edge' . ( $dashed ? ' pd-dashed' : '' );
      $tip   = '<title>' . padChartAttr ( $people [$from] [0] . ' → ' . $people [$to] [0] . ( $text !== '' ? ": $text" : '' ) ) . '</title>';
      $label = ( $nth ? "$nth. " : '' ) . $text;

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
