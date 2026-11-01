<?php

  // The railroad diagram - subs/diagrams/types/railroad.php: the rules of a grammar as syntax
  // diagrams, written in the EBNF of the W3C - the notation of XML 1.0, section 6, which the
  // railroad diagram generators read.
  //
  //   value  ::= object | array | string | 'true' | 'false' | 'null'
  //   object ::= '{' ( member ( ',' member )* )? '}'      a rule: a name, ::= and an expression,
  //   member ::= string ':' value                          on one line or on more
  //   digit  ::= [0-9]                                     a character class; #x41 one character
  //   char   ::= [#x20-#xFFFF] - '"'                       A - B: A but not B
  //   /* a comment */
  //
  // A rule is a track from a bar on the left to a double bar on the right: a terminal in a
  // rounded box, a rule's name in a square one - a link to that rule when the grammar has it -
  // a character class in a box of its own; a choice stacks its branches, the first on the
  // track; optional passes over the part, a repetition loops back under it, * is the two. Each
  // part is laid out from its own size - its width, how far it reaches above and below the
  // track - so no two parts meet, and a rule wider than the track wraps to a row under it.
  //
  // padDiagramRailroad    the SVG of every rule, one under the other
  // padDiagramRailTokens  the text as tokens, the comments and the constraints left out
  // padDiagramRailParse   the tokens of one rule as a tree: seq, alt, opt, star, plus,
  //                       except, term, nt, class, skip
  // padDiagramRailSize    the width of a part and how far it reaches above and below
  // padDiagramRailDraw    the SVG of a part at its place

  const PAD_RAIL_ARC = 10;

  function padDiagramRailroad ( $source, $title ) {

    global $padCheckSyntax;

    $source = padUnprotectQuotes ( (string) $source );

    if ( preg_match ( '/^(\s*)railroad[ \t]*(?=\r?\n|$)/', $source ) )
      $source = preg_replace ( '/^(\s*)railroad[ \t]*/', '$1', $source, 1 );

    $rules = $bad = $seen = [];

    try {
      $tokens = padDiagramRailTokens ( $source );
    } catch ( RuntimeException $e ) {
      $tokens = [];
      $bad [] = $e->getMessage ();
    }

    // The tokens a rule at a time: a name followed by ::= starts one.

    $starts = [];

    foreach ( $tokens as $i => $token )
      if ( $token [0] == 'name' and ( $tokens [$i + 1] [0] ?? '' ) == 'def' )
        $starts [] = $i;

    if ( $tokens and ( ! $starts or $starts [0] > 0 ) )
      $bad [] = 'line ' . $tokens [0] [2] . ": '" . padMakeSafe ( $tokens [0] [1], 40 ) . "' stands before any rule - write a rule as name ::= expression";

    $named = array_flip ( array_map ( fn ( $s ) => $s + 1, $starts ) );

    foreach ( $tokens as $i => $token )
      if ( $token [0] == 'def' and ! isset ( $named [$i] ) ) {
        $bad [] = 'line ' . $token [2] . ': ::= has no name before it - write a rule as name ::= expression';
        break;
      }

    foreach ( $starts as $n => $start ) {

      $name = $tokens [$start] [1];
      $line = $tokens [$start] [2];
      $end  = $starts [$n + 1] ?? count ( $tokens );
      $body = array_values ( array_filter ( array_slice ( $tokens, $start + 2, $end - $start - 2 ), fn ( $token ) => $token [0] != 'def' ) );

      if ( isset ( $seen [$name] ) ) {
        $bad [] = "the rule '" . padMakeSafe ( $name, 40 ) . "' on line $line - a rule of that name is there already, on line " . $seen [$name];
        continue;
      }

      try {
        $tree = padDiagramRailParse ( $body, $name, $line );
      } catch ( RuntimeException $e ) {
        $bad [] = $e->getMessage ();
        continue;
      }

      $seen  [$name] = $line;
      $rules []      = [ 'name' => $name, 'tree' => $tree ];

    }

    if ( $bad and $padCheckSyntax )
      padError ( 'the railroad diagram cannot read ' . $bad [0] );

    if ( ! $rules )
      return '';

    if ( count ( $rules ) > 500 ) {
      padError ( 'the railroad diagram is too large to lay out: ' . count ( $rules ) . ' rules, 500 at most - draw it in parts' );
      return '';
    }

    $parts = 0;
    foreach ( $rules as $k => $rule ) {
      $rules [$k] ['tree'] = padDiagramRailSize ( padDiagramRailWrap ( $rule ['tree'] ), $parts );
      if ( $parts > 20000 ) {
        padError ( "the railroad diagram is too large to lay out: more than 20000 parts - draw it in parts" );
        return '';
      }
    }

    // The rules one under the other: the name over its track, the bar it starts from at 12,
    // the track at 28.

    $width  = 0;
    $y      = 12;
    $places = [];

    foreach ( $rules as $k => $rule ) {
      $tree         = $rule ['tree'];
      $places [$k]  = $y + 22 + $tree ['up'];
      $y            = $places [$k] + $tree ['down'] + 22;
      $width        = max ( $width, 28 + $tree ['w'] + 24 + 12, 12 + padDiagramWidth ( $rule ['name'] ) * 1.1 + 12 );
    }

    $width  = (int) ceil ( $width );
    $height = (int) ceil ( $y - 10 );

    $desc = [];
    foreach ( $rules as $rule )
      $desc [] = $rule ['name'] . ' ::= ' . padDiagramRailText ( $rule ['tree'] );

    list ( $id, $svg ) = padChartOpen ( 'diagram', [ 'railroad', $desc ], $desc, $title, $width, $height, FALSE );

    $svg .= padDiagramStyle () . padDiagramRailStyle ();

    $links = [];
    foreach ( $rules as $k => $rule )
      $links [ $rule ['name'] ] = "$id-r$k";

    foreach ( $rules as $k => $rule ) {

      $tree = $rule ['tree'];
      $at   = $places [$k];

      list ( $draw, $exitY ) = padDiagramRailDraw ( $tree, 28, $at, $links );

      $svg .= '<g id="' . $links [ $rule ['name'] ] . '" class="pc-mark"><title>' . padDiagramEsc ( $desc [$k] ) . '</title>'
            . '<text class="pd-rr-name" x="12" y="' . padChartXY ( $at - $tree ['up'] - 10 ) . '">' . padDiagramEsc ( $rule ['name'] ) . '</text>'
            . '<path class="pd-rr-line" d="M12,' . padChartXY ( $at - 8 ) . 'V' . padChartXY ( $at + 8 ) . 'M12,' . padChartXY ( $at ) . 'H28"/>'
            . $draw
            . '<path class="pd-rr-line" d="M' . padChartXY ( 28 + $tree ['w'] ) . ',' . padChartXY ( $exitY ) . 'H' . padChartXY ( 28 + $tree ['w'] + 16 )
            . 'M' . padChartXY ( 28 + $tree ['w'] + 16 ) . ',' . padChartXY ( $exitY - 8 ) . 'V' . padChartXY ( $exitY + 8 )
            . 'M' . padChartXY ( 28 + $tree ['w'] + 21 ) . ',' . padChartXY ( $exitY - 8 ) . 'V' . padChartXY ( $exitY + 8 ) . '"/>'
            . '</g>';

    }

    return "$svg</svg>";

  }

  // The text as tokens [ kind, text, line ]: name, def (::=), term ("x" or 'x', its text
  // without the quotes), class ([...] or #xN, as written) and the operators ( ) | ? * + -.
  // A comment /* ... */ and a constraint [ wfc: ... ] or [ vc: ... ] are left out. What
  // cannot be read throws, naming its line.

  function padDiagramRailTokens ( $text ) {

    $tokens = [];
    $at     = 0;
    $line   = 1;
    $length = strlen ( $text );

    while ( $at < $length ) {

      $char = $text [$at];

      if ( $char == "\n" ) {
        $line++;
        $at++;
        continue;
      }

      if ( ctype_space ( $char ) ) {
        $at++;
        continue;
      }

      if ( substr ( $text, $at, 2 ) == '/*' ) {
        $end = strpos ( $text, '*/', $at + 2 );
        if ( $end === FALSE )
          throw new RuntimeException ( "line $line: a comment /* is never closed with */" );
        $line += substr_count ( $text, "\n", $at, $end - $at );
        $at    = $end + 2;
        continue;
      }

      if ( substr ( $text, $at, 3 ) == '::=' ) {
        $tokens [] = [ 'def', '::=', $line ];
        $at       += 3;
        continue;
      }

      if ( $char == '"' or $char == "'" ) {
        $end = strpos ( $text, $char, $at + 1 );
        $eol = strpos ( $text, "\n", $at + 1 );
        if ( $end === FALSE or ( $eol !== FALSE and $eol < $end ) )
          throw new RuntimeException ( "line $line: the quote $char before '" . padMakeSafe ( substr ( $text, $at + 1, 20 ), 20 ) . "' is never closed" );
        $tokens [] = [ 'term', substr ( $text, $at + 1, $end - $at - 1 ), $line ];
        $at        = $end + 1;
        continue;
      }

      if ( $char == '[' ) {
        $end = strpos ( $text, ']', $at + 1 );
        $eol = strpos ( $text, "\n", $at + 1 );
        if ( $end === FALSE or ( $eol !== FALSE and $eol < $end ) )
          throw new RuntimeException ( "line $line: the character class [ before '" . padMakeSafe ( substr ( $text, $at + 1, 20 ), 20 ) . "' is never closed with ]" );
        $class = substr ( $text, $at, $end - $at + 1 );
        if ( ! preg_match ( '/^\[\s*(wfc|vc)\s*:/i', $class ) )
          $tokens [] = [ 'class', $class, $line ];
        $at = $end + 1;
        continue;
      }

      if ( preg_match ( '/\G#x[0-9a-fA-F]+/', $text, $m, 0, $at ) ) {
        $tokens [] = [ 'class', $m [0], $line ];
        $at       += strlen ( $m [0] );
        continue;
      }

      if ( preg_match ( '/\G[\p{L}_][\p{L}\p{M}\p{N}_.\-]*/u', $text, $m, 0, $at ) ) {
        $name      = rtrim ( $m [0], '-' );
        $tokens [] = [ 'name', $name, $line ];
        $at       += strlen ( $name );
        continue;
      }

      if ( strpos ( '()|?*+-', $char ) !== FALSE ) {
        $tokens [] = [ $char, $char, $line ];
        $at++;
        continue;
      }

      throw new RuntimeException ( "line $line: '" . padMakeSafe ( mb_substr ( substr ( $text, $at ), 0, 1 ), 4 ) . "' - a rule is written with names, 'terminals', [classes], #xN, ( ) | ? * + and -" );

    }

    return $tokens;

  }

  // The tokens of one rule as its tree. Throws what it cannot read, naming the rule.

  function padDiagramRailParse ( $tokens, $name, $line ) {

    $at   = 0;
    $fail = function ( $why ) use ( $name, $line ) {
      throw new RuntimeException ( "the rule '" . padMakeSafe ( $name, 40 ) . "' on line $line - $why" );
    };

    $peek = function () use ( &$at, $tokens ) {
      return $tokens [$at] [0] ?? 'end';
    };

    $alt = $seq = $item = NULL;

    $alt = function () use ( &$seq, &$at, $tokens, $peek, $fail ) {
      $list = [ $seq () ];
      while ( $peek () == '|' ) {
        $at++;
        $list [] = $seq ();
      }
      foreach ( $list as $one )
        if ( $one === NULL )
          $fail ( 'a choice has an empty branch: write ( A )? for a part that may be left out' );
      if ( count ( $list ) == 1 )
        return $list [0];
      $flat = [];
      foreach ( $list as $one )
        if ( $one [0] == 'alt' )
          array_push ( $flat, ...$one [1] );
        else
          $flat [] = $one;
      return [ 'alt', $flat ];
    };

    $seq = function () use ( &$item, $peek ) {
      $list = [];
      while ( ! in_array ( $peek (), [ ')', '|', 'end' ] ) )
        $list [] = $item ();
      if ( ! $list )
        return NULL;
      $list = array_values ( array_filter ( $list, fn ( $one ) => $one [0] != 'skip' ) ) ?: [ [ 'skip' ] ];
      if ( count ( $list ) == 1 )
        return $list [0];
      $flat = [];
      foreach ( $list as $one )
        if ( $one [0] == 'seq' )
          array_push ( $flat, ...$one [1] );
        else
          $flat [] = $one;
      return [ 'seq', $flat ];
    };

    $primary = function () use ( &$alt, &$at, $tokens, $peek, $fail ) {
      $token = $tokens [$at] ?? [ 'end', '', 0 ];
      switch ( $token [0] ) {
        case 'name':  $at++; return [ 'nt', $token [1] ];
        case 'class': $at++; return [ 'class', $token [1] ];
        case 'term':  $at++; return $token [1] === '' ? [ 'skip' ] : [ 'term', $token [1] ];
        case '(':
          $at++;
          if ( $peek () == ')' )
            $fail ( 'a group ( ) is empty' );
          $inner = $alt ();
          if ( $peek () != ')' )
            $fail ( 'a ( is never closed with )' );
          $at++;
          return $inner;
      }
      $fail ( $token [0] == 'end' ? 'it ends where a part should follow' : "a $token[1] follows nothing it could apply to" );
    };

    $item = function () use ( &$primary, &$at, $peek, $tokens ) {
      $one = $primary ();
      while ( in_array ( $peek (), [ '?', '*', '+' ] ) ) {
        $op  = $tokens [$at++] [0];
        $one = [ $op == '?' ? 'opt' : ( $op == '*' ? 'star' : 'plus' ), $one ];
      }
      if ( $peek () == '-' ) {
        $at++;
        $not = $primary ();
        while ( in_array ( $peek (), [ '?', '*', '+' ] ) ) {
          $op  = $tokens [$at++] [0];
          $not = [ $op == '?' ? 'opt' : ( $op == '*' ? 'star' : 'plus' ), $not ];
        }
        $one = [ 'except', $one, $not ];
      }
      return $one;
    };

    if ( ! $tokens )
      $fail ( 'it has nothing after ::=' );

    $tree = $alt ();

    if ( $peek () == ')' )
      $fail ( 'a ) closes nothing' );

    return $tree;

  }

  // The tree written back as text, for the description and the tooltips: a group in
  // parentheses where its operator binds less than the one round it, a terminal in
  // typographic quotes - “text” - which read as quotes and hold a ' or a " of the terminal
  // without an entity for Tidy to write over (padDiagramEsc).

  function padDiagramRailText ( $tree, $in = '' ) {

    $group = fn ( $one ) => in_array ( $one [0], [ 'seq', 'alt', 'except', 'stack' ] ) ? '(' . padDiagramRailText ( $one ) . ')' : padDiagramRailText ( $one );

    switch ( $tree [0] ) {
      case 'stack':
      case 'seq':    return implode ( ' ', array_map ( fn ( $one ) => $one [0] == 'alt' ? '(' . padDiagramRailText ( $one ) . ')' : padDiagramRailText ( $one ), $tree [1] ) );
      case 'alt':    return implode ( ' | ', array_map ( 'padDiagramRailText', $tree [1] ) );
      case 'opt':    return isset ( $tree ['star'] ) ? $group ( $tree [1] [1] ) . '*' : $group ( $tree [1] ) . '?';
      case 'star':   return $group ( $tree [1] ) . '*';
      case 'plus':   return $group ( $tree [1] ) . '+';
      case 'except': return $group ( $tree [1] ) . ' - ' . $group ( $tree [2] );
      case 'term':   return '“' . $tree [1] . '”';
      case 'skip':   return '“”';
    }

    return $tree [1];

  }

  // A rule wider than the track - 640 - has its sequence in rows of it, a row under the other.
  // Only the rule's own sequence wraps: a part inside it stays whole.

  function padDiagramRailWrap ( $tree ) {

    if ( $tree [0] != 'seq' )
      return $tree;

    $count = 0;
    $sizes = array_map ( fn ( $one ) => padDiagramRailSize ( $one, $count ) ['w'], $tree [1] );

    if ( array_sum ( $sizes ) + 12 * ( count ( $sizes ) - 1 ) <= 640 )
      return $tree;

    $rows = [ [] ];
    $used = 0;

    foreach ( $tree [1] as $i => $one ) {
      $need = $sizes [$i] + ( end ( $rows ) ? 12 : 0 );
      if ( end ( $rows ) and $used + $need > 640 - 4 * PAD_RAIL_ARC ) {
        $rows [] = [];
        $used    = 0;
        $need    = $sizes [$i];
      }
      $rows [ count ( $rows ) - 1 ] [] = $one;
      $used += $need;
    }

    return [ 'stack', array_map ( fn ( $row ) => count ( $row ) == 1 ? $row [0] : [ 'seq', $row ], $rows ) ];

  }

  // The size of a part: w, its width; up and down, how far it reaches above and below the
  // track it is on. A box is 24 high; a choice keeps 10 between its branches and room for the
  // two turns of a branch, an optional part's way over it and a loop's way back under it are
  // 10 clear of the part. $count counts the parts, for the limit.

  function padDiagramRailSize ( $tree, &$count ) {

    $r = PAD_RAIL_ARC;
    $count++;

    switch ( $tree [0] ) {

      case 'term':
      case 'class':
        return $tree + [ 'w' => mb_strwidth ( $tree [1] ) * 7.3 + 20, 'up' => 12, 'down' => 12 ];

      case 'nt':
        return $tree + [ 'w' => padDiagramWidth ( $tree [1] ) + 20, 'up' => 12, 'down' => 12 ];

      case 'skip':
        return $tree + [ 'w' => 0, 'up' => 0, 'down' => 0 ];

      case 'seq':
        $tree [1] = array_map ( fn ( $one ) => padDiagramRailSize ( $one, $count ), $tree [1] );
        return $tree + [ 'w' => array_sum ( array_column ( $tree [1], 'w' ) ) + 12 * ( count ( $tree [1] ) - 1 ),
                         'up' => max ( array_column ( $tree [1], 'up' ) ), 'down' => max ( array_column ( $tree [1], 'down' ) ) ];

      case 'alt':
        $tree [1] = array_map ( fn ( $one ) => padDiagramRailSize ( $one, $count ), $tree [1] );
        $y        = 0;
        $ys       = [ 0 ];
        foreach ( $tree [1] as $i => $one )
          if ( $i ) {
            $y    = max ( $y + 2 * $r, $y + $tree [1] [$i - 1] ['down'] + 10 + $one ['up'] );
            $ys [] = $y;
          }
        $last = end ( $tree [1] );
        return $tree + [ 'w' => max ( array_column ( $tree [1], 'w' ) ) + 4 * $r, 'up' => $tree [1] [0] ['up'],
                         'down' => $y + $last ['down'], 'ys' => $ys ];

      case 'opt':
        $tree [1] = padDiagramRailSize ( $tree [1], $count );
        $over     = max ( 2 * $r, $tree [1] ['up'] + 10 );
        return $tree + [ 'w' => $tree [1] ['w'] + 4 * $r, 'up' => $over, 'down' => $tree [1] ['down'], 'over' => $over ];

      case 'plus':
        $tree [1] = padDiagramRailSize ( $tree [1], $count );
        $under    = max ( 2 * $r, $tree [1] ['down'] + 10 );
        return $tree + [ 'w' => $tree [1] ['w'] + 2 * $r, 'up' => $tree [1] ['up'], 'down' => $under + 1, 'under' => $under ];

      case 'star':
        return padDiagramRailSize ( [ 'opt', [ 'plus', $tree [1] ] ], $count ) + [ 'star' => TRUE ];

      case 'except':
        $tree [1] = padDiagramRailSize ( $tree [1], $count );
        $note     = 'except ' . padDiagramRailText ( $tree [2] );
        return $tree + [ 'w' => max ( $tree [1] ['w'], padDiagramWidth ( $note ) * 0.95 + 8 ), 'up' => $tree [1] ['up'],
                         'down' => $tree [1] ['down'] + 18, 'note' => $note ];

      case 'stack':
        $tree [1] = array_map ( fn ( $one ) => padDiagramRailSize ( $one, $count ), $tree [1] );
        $y        = 0;
        $ys       = [ 0 ];
        foreach ( $tree [1] as $i => $one )
          if ( $i ) {
            $back  = $y + max ( 2 * $r, $tree [1] [$i - 1] ['down'] + 10 );
            $y     = $back + max ( 2 * $r, $one ['up'] + 10 );
            $ys [] = $y;
          }
        return $tree + [ 'w' => max ( array_column ( $tree [1], 'w' ) ) + 4 * $r, 'up' => $tree [1] [0] ['up'],
                         'down' => $y + end ( $tree [1] ) ['down'], 'ys' => $ys ];

    }

    return $tree + [ 'w' => 0, 'up' => 0, 'down' => 0 ];

  }

  // A part drawn with its entry at x, y: [ svg, the y of its exit ] - its exit is at x + w,
  // on the track it came in on, but for the rows of a wrapped rule, which leave on the last.
  // $links are the ids of the rules, by name, for the boxes of the names to point to.

  function padDiagramRailDraw ( $tree, $x, $y, $links ) {

    $r  = PAD_RAIL_ARC;
    $P  = fn ( $v ) => padChartXY ( $v );
    $H  = fn ( $from, $to, $at ) => abs ( $to - $from ) < 0.05 ? '' : '<path class="pd-rr-line" d="M' . $P ( $from ) . ',' . $P ( $at ) . 'H' . $P ( $to ) . '"/>';

    switch ( $tree [0] ) {

      case 'term':
      case 'class':
      case 'nt':
        $class = [ 'term' => 'pd-rr-term', 'class' => 'pd-rr-class', 'nt' => 'pd-rr-nt' ] [ $tree [0] ];
        $box   = '<rect class="' . $class . '" x="' . $P ( $x ) . '" y="' . $P ( $y - 12 ) . '" width="' . $P ( $tree ['w'] ) . '" height="24" rx="'
               . ( $tree [0] == 'term' ? 12 : ( $tree [0] == 'class' ? 4 : 0 ) ) . '"/>'
               . '<text class="' . $class . '-text" x="' . $P ( $x + $tree ['w'] / 2 ) . '" y="' . $P ( $y + 4 ) . '" text-anchor="middle">' . padDiagramEsc ( $tree [1] ) . '</text>';
        if ( $tree [0] == 'nt' and isset ( $links [ $tree [1] ] ) )
          $box = '<a href="#' . $links [ $tree [1] ] . '">' . $box . '</a>';
        return [ $box, $y ];

      case 'skip':
        return [ '', $y ];

      case 'seq':
        $svg = '';
        $at  = $x;
        foreach ( $tree [1] as $i => $one ) {
          if ( $i ) {
            $svg .= $H ( $at, $at + 12, $y );
            $at  += 12;
          }
          $svg .= padDiagramRailDraw ( $one, $at, $y, $links ) [0];
          $at  += $one ['w'];
        }
        return [ $svg, $y ];

      case 'alt':
        $inner = $tree ['w'] - 4 * $r;
        $svg   = '';
        foreach ( $tree [1] as $i => $one ) {
          $by   = $y + $tree ['ys'] [$i];
          $from = $x + 2 * $r + ( $inner - $one ['w'] ) / 2;
          if ( $i )
            $svg .= '<path class="pd-rr-line" d="M' . $P ( $x ) . ',' . $P ( $y ) . "a$r,$r 0 0 1 $r,$r" . 'V' . $P ( $by - $r ) . "a$r,$r 0 0 0 $r,$r"
                  . 'H' . $P ( $from ) . 'M' . $P ( $from + $one ['w'] ) . ',' . $P ( $by ) . 'H' . $P ( $x + $tree ['w'] - 2 * $r )
                  . "a$r,$r 0 0 0 $r,-$r" . 'V' . $P ( $y + $r ) . "a$r,$r 0 0 1 $r,-$r" . '"/>';
          else
            $svg .= $H ( $x, $from, $y ) . $H ( $from + $one ['w'], $x + $tree ['w'], $y );
          $svg .= padDiagramRailDraw ( $one, $from, $by, $links ) [0];
        }
        return [ $svg, $y ];

      case 'opt':
        $one  = $tree [1];
        $top  = $y - $tree ['over'];
        $svg  = '<path class="pd-rr-line" d="M' . $P ( $x ) . ',' . $P ( $y ) . "a$r,$r 0 0 0 $r,-$r" . 'V' . $P ( $top + $r ) . "a$r,$r 0 0 1 $r,-$r"
              . 'H' . $P ( $x + $tree ['w'] - 2 * $r ) . "a$r,$r 0 0 1 $r,$r" . 'V' . $P ( $y - $r ) . "a$r,$r 0 0 0 $r,$r" . '"/>'
              . $H ( $x, $x + 2 * $r, $y ) . $H ( $x + 2 * $r + $one ['w'], $x + $tree ['w'], $y );
        return [ $svg . padDiagramRailDraw ( $one, $x + 2 * $r, $y, $links ) [0], $y ];

      case 'plus':
        $one   = $tree [1];
        $low   = $y + $tree ['under'];
        $mid   = $x + $tree ['w'] / 2;
        $svg   = '<path class="pd-rr-line" d="M' . $P ( $x + $tree ['w'] - $r ) . ',' . $P ( $y ) . "a$r,$r 0 0 1 $r,$r" . 'V' . $P ( $low - $r ) . "a$r,$r 0 0 1 -$r,$r"
               . 'H' . $P ( $x + $r ) . "a$r,$r 0 0 1 -$r,-$r" . 'V' . $P ( $y + $r ) . "a$r,$r 0 0 1 $r,-$r" . '"/>'
               . '<path class="pd-rr-arrow" d="M' . $P ( $mid + 3 ) . ',' . $P ( $low - 4 ) . 'L' . $P ( $mid - 3 ) . ',' . $P ( $low ) . 'L' . $P ( $mid + 3 ) . ',' . $P ( $low + 4 ) . '"/>'
               . $H ( $x, $x + $r, $y ) . $H ( $x + $r + $one ['w'], $x + $tree ['w'], $y );
        return [ $svg . padDiagramRailDraw ( $one, $x + $r, $y, $links ) [0], $y ];

      case 'except':
        $one  = $tree [1];
        $from = $x + ( $tree ['w'] - $one ['w'] ) / 2;
        $svg  = $H ( $x, $from, $y ) . $H ( $from + $one ['w'], $x + $tree ['w'], $y )
              . padDiagramRailDraw ( $one, $from, $y, $links ) [0]
              . '<text class="pd-rr-note" x="' . $P ( $x + $tree ['w'] / 2 ) . '" y="' . $P ( $y + $one ['down'] + 14 ) . '" text-anchor="middle">' . padDiagramEsc ( $tree ['note'] ) . '</text>';
        return [ $svg, $y ];

      case 'stack':
        $svg  = '';
        $last = count ( $tree [1] ) - 1;
        $end  = $x + $tree ['w'] - 2 * $r;
        foreach ( $tree [1] as $i => $one ) {
          $ry    = $y + $tree ['ys'] [$i];
          $start = $i ? $x + 2 * $r : $x;
          if ( ! $i )
            $svg .= $H ( $x, $x + 2 * $r, $ry );
          $svg .= padDiagramRailDraw ( $one, $x + 2 * $r, $ry, $links ) [0];
          if ( $i < $last ) {
            $next = $y + $tree ['ys'] [$i + 1];
            $back = $ry + max ( 2 * $r, $one ['down'] + 10 );
            $svg .= '<path class="pd-rr-line" d="M' . $P ( $x + 2 * $r + $one ['w'] ) . ',' . $P ( $ry ) . 'H' . $P ( $end ) . "a$r,$r 0 0 1 $r,$r" . 'V' . $P ( $back - $r )
                  . "a$r,$r 0 0 1 -$r,$r" . 'H' . $P ( $x + 2 * $r ) . "a$r,$r 0 0 0 -$r,$r" . 'V' . $P ( $next - $r ) . "a$r,$r 0 0 0 $r,$r" . '"/>';
          } else
            $svg .= $H ( $x + 2 * $r + $one ['w'], $x + $tree ['w'], $ry );
        }
        return [ $svg, $y + $tree ['ys'] [$last] ];

    }

    return [ '', $y ];

  }

  // The rules of the railroad diagram: the track in the colour of the edges, a terminal in the
  // colours of a round node and in a fixed-width face, a rule's name in those of a node, a
  // class in those of a decision; a link to a rule underlined where it is pointed at or has
  // the focus.

  function padDiagramRailStyle () {

    return '<style' . padStyleNonce () . '>'
         . '.pad-chart-diagram .pd-rr-line{fill:none;stroke:var(--pad-diagram-edge);stroke-width:1.5}'
         . '.pad-chart-diagram .pd-rr-arrow{fill:none;stroke:var(--pad-diagram-edge);stroke-width:1.5;stroke-linejoin:round}'
         . '.pad-chart-diagram .pd-rr-term{fill:var(--pad-diagram-round);stroke:var(--pad-diagram-round-border);stroke-width:1.5}'
         . '.pad-chart-diagram .pd-rr-nt{fill:var(--pad-diagram-node);stroke:var(--pad-diagram-border);stroke-width:1.5}'
         . '.pad-chart-diagram .pd-rr-class{fill:var(--pad-diagram-decision);stroke:var(--pad-diagram-decision-border);stroke-width:1.5}'
         . '.pad-chart-diagram .pd-rr-term-text,.pad-chart-diagram .pd-rr-class-text{font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace}'
         . '.pad-chart-diagram .pd-rr-name{font-weight:700}'
         . '.pad-chart-diagram .pd-rr-note{font-size:11px;font-style:italic}'
         . '.pad-chart-diagram a:hover .pd-rr-nt-text,.pad-chart-diagram a:focus .pd-rr-nt-text{text-decoration:underline}'
         . '.pad-chart-diagram a:focus-visible .pd-rr-nt{stroke-width:3}'
         . '</style>';

  }

?>
