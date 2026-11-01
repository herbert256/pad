<?php

  // Go - the go notation, {go '(;SZ[9]AB[cc][gg]AW[cg];B[ee];W[gc])'/}. A go position from a
  // game record in SGF, the Smart Game Format every go program reads and writes, drawn as an
  // SVG board: the grid, the star points, the coordinates, the stones - a game's moves played
  // out with their captures - its last move marked, or every move numbered.
  //
  // padGoParse     the main line of an SGF text: its nodes, each a list of properties with
  //                their values - the first variation of every branch - or the reason it is
  //                no SGF
  // padGoPlay      the position after the moves: the stones of the setup (AB, AW, AE), then
  //                every move (B, W) with the stones it captures taken off
  // padGoSvg       the board
  //
  // The properties read: SZ the size (9, 13 and 19 have their star points; 2 to 25), AB AW AE
  // the stones set up and taken away (point lists aa:cc as well), B and W the moves (an empty
  // value or tt a pass), PL the player to move, GN the game's name, and the markup a diagram
  // carries: LB labels, TR CR SQ MA marks. The others - the players, the result, comments,
  // the time - are read and left out.

  const PAD_GO_COLOURS = [ 'board' => [ '#e3b866', '#a98545' ], 'line' => [ '#3d2f1a', '#2a2012' ],
                           'black' => [ '#1d1d1d', '#111111' ], 'white' => [ '#fbfbf8', '#e9e9e4' ],
                           'text'  => [ '#5b4a2c', '#cdbb94' ], 'mark' => [ '#c8102e', '#ff5a6e' ] ];

  // The star points of the usual boards, 0-based from the top left.

  const PAD_GO_STARS = [ 9 => [ 2, 4, 6 ], 13 => [ 3, 6, 9 ], 19 => [ 3, 9, 15 ] ];

  // The letters of the columns: A to T without I, as go writes them.

  const PAD_GO_COLUMNS = 'ABCDEFGHJKLMNOPQRSTUVWXYZ';

  function padGoParse ( $text ) {

    $text   = (string) $text;
    $length = strlen ( $text );
    $at     = 0;

    $skip = function () use ( $text, $length, &$at ) {
      while ( $at < $length and ctype_space ( $text [$at] ) )
        $at++;
    };

    $skip ();

    if ( $at >= $length )
      return [];

    if ( $text [$at] != '(' )
      return 'it does not start with (';

    // A game tree: ( sequence of nodes, then its variations ). The main line goes on into the
    // first variation; the others are read past. The trees open are a stack, each whether it
    // is on the main line and whether its first variation is still to come, and the nodes of
    // the main line go into one list: a function calling itself per ( ended a record nested
    // 50 000 deep with PHP's "Maximum call stack size reached", and merged the main line into
    // the tree around it once per level.

    $at++;
    $open  = [ [ TRUE, TRUE ] ];
    $nodes = [];

    while ( $open ) {

      $skip ();

      if ( $at >= $length )
        return 'a ( never closes';

      $char = $text [$at];

      if ( $char == ')' ) {
        $at++;
        array_pop ( $open );
        continue;
      }

      if ( $char == '(' ) {
        $at++;
        $top = array_key_last ( $open );
        $open [] = [ $open [$top] [0] and $open [$top] [1], TRUE ];
        $open [$top] [1] = FALSE;
        continue;
      }

      if ( $char != ';' )
        return "it has a '$char' where a node starts with ;";

      $at++;
      $node = [];

      while ( TRUE ) {

        $skip ();

        if ( ! preg_match ( '/\G([A-Z]+)/', $text, $m, 0, $at ) )
          break;

        $name = $m [1];
        $at  += strlen ( $name );
        $skip ();

        if ( ( $text [$at] ?? '' ) != '[' )
          return "the property $name has no value in [ ]";

        while ( ( $text [$at] ?? '' ) == '[' ) {
          $at++;
          $value = '';
          while ( $at < $length and $text [$at] != ']' ) {
            if ( $text [$at] == '\\' and $at + 1 < $length )
              $at++;
            $value .= $text [$at++];
          }
          if ( $at >= $length )
            return "a value of $name never closes with ]";
          $at++;
          $node [$name] [] = $value;
          $skip ();
        }

      }

      if ( end ( $open ) [0] )
        $nodes [] = $node;

    }

    return $nodes;

  }

  // A point of SGF - two letters, the column and the row from the top left, aa the corner -
  // as [ column, row ], NULL for a pass, FALSE when it is off the board.

  function padGoPoint ( $value, $size ) {

    $value = trim ( $value );

    if ( $value === '' or ( $value === 'tt' and $size <= 19 ) )
      return NULL;

    if ( ! preg_match ( '/^[a-zA-Z]{2}$/D', $value ) )
      return FALSE;

    $code = fn ( $c ) => ctype_lower ( $c ) ? ord ( $c ) - 97 : ord ( $c ) - 65 + 26;

    $x = $code ( $value [0] );
    $y = $code ( $value [1] );

    return ( $x < $size and $y < $size ) ? [ $x, $y ] : FALSE;

  }

  // The points of a list value: a point, or a rectangle aa:cc.

  function padGoPoints ( $value, $size ) {

    $ends = explode ( ':', $value );

    if ( count ( $ends ) == 1 ) {
      $point = padGoPoint ( $value, $size );
      return ( $point === FALSE ) ? FALSE : ( $point === NULL ? [] : [ $point ] );
    }

    $from = padGoPoint ( $ends [0], $size );
    $to   = padGoPoint ( $ends [1], $size );

    if ( ! $from or ! $to or count ( $ends ) != 2 )
      return FALSE;

    $points = [];

    for ( $y = min ( $from [1], $to [1] ); $y <= max ( $from [1], $to [1] ); $y++ )
      for ( $x = min ( $from [0], $to [0] ); $x <= max ( $from [0], $to [0] ); $x++ )
        $points [] = [ $x, $y ];

    return $points;

  }

  // The name of a point as go writes it: its column letter and its row from the bottom, D4.

  function padGoName ( $point, $size ) {

    return PAD_GO_COLUMNS [ $point [0] ] . ( $size - $point [1] );

  }

  // The group of a stone and its liberties.

  function padGoGroup ( $board, $start, $size ) {

    $colour = $board [ $start [1] ] [ $start [0] ];
    $seen   = [ $start [0] . ',' . $start [1] => $start ];
    $todo   = [ $start ];
    $free   = 0;

    while ( $todo ) {
      list ( $x, $y ) = array_pop ( $todo );
      foreach ( [ [ 1, 0 ], [ -1, 0 ], [ 0, 1 ], [ 0, -1 ] ] as list ( $dx, $dy ) ) {
        $nx = $x + $dx;
        $ny = $y + $dy;
        if ( $nx < 0 or $ny < 0 or $nx >= $size or $ny >= $size )
          continue;
        $there = $board [$ny] [$nx];
        if ( $there === '' )
          $free++;
        elseif ( $there === $colour and ! isset ( $seen [ "$nx,$ny" ] ) ) {
          $seen [ "$nx,$ny" ] = [ $nx, $ny ];
          $todo [] = [ $nx, $ny ];
        }
      }
    }

    return [ array_values ( $seen ), $free ];

  }

  // The game played out: [ board, moves [ [ colour, point, number ] ], captured by black and
  // by white, the markup, the problems ] after $upto moves (all when 0).

  function padGoPlay ( $nodes, $size, $upto ) {

    $board    = array_fill ( 0, $size, array_fill ( 0, $size, '' ) );
    $numbers  = [];
    $moves    = [];
    $captured = [ 'B' => 0, 'W' => 0 ];
    $marks    = [];
    $problems = [];
    $count    = 0;

    foreach ( $nodes as $node ) {

      if ( $upto and $count >= $upto and ( isset ( $node ['B'] ) or isset ( $node ['W'] ) ) )
        break;

      // The markup of a diagram is the node's own: what the board shows is the markup of the
      // node shown last, as a viewer shows it.

      $marks = [];

      foreach ( [ 'AB' => 'B', 'AW' => 'W', 'AE' => '' ] as $property => $colour )
        foreach ( $node [$property] ?? [] as $value ) {
          $points = padGoPoints ( $value, $size );
          if ( $points === FALSE ) {
            $problems [] = "has a point '$value' off the board in $property";
            continue;
          }
          foreach ( $points as list ( $x, $y ) ) {
            $board [$y] [$x] = $colour;
            unset ( $numbers [ "$x,$y" ] );
          }
        }

      foreach ( [ 'B', 'W' ] as $colour )
        foreach ( $node [$colour] ?? [] as $value ) {

          $point = padGoPoint ( $value, $size );

          if ( $point === FALSE ) {
            $problems [] = "has a move $colour" . "[$value] off the board";
            continue;
          }

          $count++;

          if ( $point === NULL ) {
            $moves [] = [ $colour, NULL, $count ];
            continue;
          }

          list ( $x, $y ) = $point;

          if ( $board [$y] [$x] !== '' )
            $problems [] = "has a move $colour" . "[$value] on a point that is taken";

          $board [$y] [$x]       = $colour;
          $numbers [ "$x,$y" ]   = $count;
          $moves []              = [ $colour, $point, $count ];
          $other                 = ( $colour == 'B' ) ? 'W' : 'B';

          // The groups of the other colour next to the stone, without a liberty left, are
          // taken off; then the stone's own group, when it has none - a suicide, which some
          // rules allow.

          foreach ( [ [ 1, 0 ], [ -1, 0 ], [ 0, 1 ], [ 0, -1 ] ] as list ( $dx, $dy ) ) {
            $nx = $x + $dx;
            $ny = $y + $dy;
            if ( $nx < 0 or $ny < 0 or $nx >= $size or $ny >= $size or $board [$ny] [$nx] !== $other )
              continue;
            list ( $group, $free ) = padGoGroup ( $board, [ $nx, $ny ], $size );
            if ( $free == 0 )
              foreach ( $group as list ( $gx, $gy ) ) {
                $board [$gy] [$gx] = '';
                unset ( $numbers [ "$gx,$gy" ] );
                $captured [$colour]++;
              }
          }

          list ( $group, $free ) = padGoGroup ( $board, $point, $size );

          if ( $free == 0 )
            foreach ( $group as list ( $gx, $gy ) ) {
              $board [$gy] [$gx] = '';
              unset ( $numbers [ "$gx,$gy" ] );
              $captured [$other]++;
            }

        }

      foreach ( [ 'TR' => 'triangle', 'CR' => 'circle', 'SQ' => 'square', 'MA' => 'cross' ] as $property => $shape )
        foreach ( $node [$property] ?? [] as $value )
          foreach ( padGoPoints ( $value, $size ) ?: [] as $point )
            $marks [ $point [0] . ',' . $point [1] ] = [ $shape, '' ];

      foreach ( $node ['LB'] ?? [] as $value ) {
        $parts = explode ( ':', $value, 2 );
        $point = padGoPoint ( $parts [0], $size );
        if ( $point and isset ( $parts [1] ) )
          $marks [ $point [0] . ',' . $point [1] ] = [ 'label', $parts [1] ];
      }

    }

    return [ $board, $numbers, $moves, $captured, $marks, $problems ];

  }

  // The board: a point every 40 units, the coordinates in a margin of 40 around it - the
  // columns A to T without I along the top and the bottom, the rows from the bottom up the
  // sides - the star points, the stones, the marks; the last move a ring, or with $numbered
  // every stone that a move put there and that is still there its number.

  function padGoSvg ( $played, $size, $numbered, $coordinates, $pixels, $id, $title, $desc ) {

    list ( $board, $numbers, $moves, $captured, $marks ) = $played;

    $cell   = 40;
    $margin = $coordinates ? 44 : 26;
    $span   = ( $size - 1 ) * $cell;
    $full   = $span + 2 * $margin;
    $at     = fn ( $n ) => $margin + $n * $cell;

    $svg = '<rect class="pgo-board" x="0" y="0" width="' . $full . '" height="' . $full . '" rx="6"/>';

    $lines = '';
    for ( $i = 0; $i < $size; $i++ )
      $lines .= 'M' . $margin . ' ' . $at ( $i ) . 'h' . $span . 'M' . $at ( $i ) . ' ' . $margin . 'v' . $span;

    $svg .= '<path class="pgo-line" d="' . $lines . '"/>'
          . '<rect class="pgo-edge" x="' . $margin . '" y="' . $margin . '" width="' . $span . '" height="' . $span . '"/>';

    // A 19 board has nine star points; the 9 and the 13 board their four corner points and the
    // middle one.

    $middle = intdiv ( $size, 2 );

    foreach ( PAD_GO_STARS [$size] ?? [] as $sx )
      foreach ( PAD_GO_STARS [$size] as $sy )
        if ( $size == 19 or ( $sx != $middle ) == ( $sy != $middle ) )
          $svg .= '<circle class="pgo-star" cx="' . $at ( $sx ) . '" cy="' . $at ( $sy ) . '" r="5"/>';

    if ( $coordinates )
      for ( $i = 0; $i < $size; $i++ ) {
        $letter = PAD_GO_COLUMNS [$i];
        $svg .= '<text class="pgo-coord" x="' . $at ( $i ) . '" y="' . ( $margin - 22 ) . '" text-anchor="middle">' . $letter . '</text>'
              . '<text class="pgo-coord" x="' . $at ( $i ) . '" y="' . ( $full - 12 ) . '" text-anchor="middle">' . $letter . '</text>'
              . '<text class="pgo-coord" x="' . ( $margin - 24 ) . '" y="' . ( $at ( $i ) + 6 ) . '" text-anchor="middle">' . ( $size - $i ) . '</text>'
              . '<text class="pgo-coord" x="' . ( $full - 20 ) . '" y="' . ( $at ( $i ) + 6 ) . '" text-anchor="middle">' . ( $size - $i ) . '</text>';
      }

    $last = NULL;
    foreach ( array_reverse ( $moves ) as $move )
      if ( $move [1] !== NULL ) {
        $last = $move;
        break;
      }

    for ( $y = 0; $y < $size; $y++ )
      for ( $x = 0; $x < $size; $x++ ) {

        $stone = $board [$y] [$x];

        if ( $stone === '' )
          continue;

        $svg .= '<circle class="pgo-' . ( $stone == 'B' ? 'b' : 'w' ) . '" cx="' . $at ( $x ) . '" cy="' . $at ( $y ) . '" r="19"/>';

        if ( $numbered and isset ( $numbers [ "$x,$y" ] ) )
          $svg .= '<text class="pgo-num pgo-on' . $stone . '" x="' . $at ( $x ) . '" y="' . ( $at ( $y ) + 6 ) . '" text-anchor="middle">' . $numbers [ "$x,$y" ] . '</text>';
        elseif ( ! $numbered and $last and $last [1] == [ $x, $y ] and ! isset ( $marks [ "$x,$y" ] ) )
          $svg .= '<circle class="pgo-last pgo-on' . $stone . '" cx="' . $at ( $x ) . '" cy="' . $at ( $y ) . '" r="9"/>';

      }

    // The marks: a shape on a stone or a point, a label with the board cleared under it.

    foreach ( $marks as $key => list ( $shape, $text ) ) {

      list ( $x, $y ) = array_map ( 'intval', explode ( ',', $key ) );

      $cx    = $at ( $x );
      $cy    = $at ( $y );
      $on    = $board [$y] [$x];
      $class = 'pgo-mark' . ( $on !== '' ? " pgo-on$on" : '' );

      if ( $shape == 'label' ) {
        if ( $on === '' )
          $svg .= '<circle class="pgo-clear" cx="' . $cx . '" cy="' . $cy . '" r="13"/>';
        $svg .= '<text class="pgo-num' . ( $on !== '' ? " pgo-on$on" : '' ) . '" x="' . $cx . '" y="' . ( $cy + 6 ) . '" text-anchor="middle">' . padNotationAttr ( mb_substr ( $text, 0, 3 ) ) . '</text>';
      } elseif ( $shape == 'circle' )
        $svg .= '<circle class="' . $class . '" cx="' . $cx . '" cy="' . $cy . '" r="9"/>';
      elseif ( $shape == 'square' )
        $svg .= '<rect class="' . $class . '" x="' . ( $cx - 8 ) . '" y="' . ( $cy - 8 ) . '" width="16" height="16"/>';
      elseif ( $shape == 'triangle' )
        $svg .= '<path class="' . $class . '" d="M' . $cx . ' ' . ( $cy - 11 ) . 'l10 17h-20z"/>';
      else
        $svg .= '<path class="' . $class . '" d="M' . ( $cx - 8 ) . ' ' . ( $cy - 8 ) . 'l16 16m0 -16l-16 16"/>';

    }

    $scale = $pixels / $full;

    return '<svg xmlns="http://www.w3.org/2000/svg" class="pad-go" role="img" aria-labelledby="' . "$id-title $id-desc" . '"'
         . ' width="' . $pixels . '" height="' . $pixels . '" viewBox="0 0 ' . $full . ' ' . $full . '">'
         . "<title id=\"$id-title\">" . padNotationAttr ( $title ) . '</title>'
         . "<desc id=\"$id-desc\">" . padNotationAttr ( $desc ) . '</desc>'
         . $svg . '</svg>';

  }

  // The position in words: the stones of each colour by their points, the last move, the
  // captures.

  function padGoDescribe ( $played, $size ) {

    list ( $board, $numbers, $moves, $captured ) = $played;

    $parts = [];

    foreach ( [ 'B' => 'Black', 'W' => 'White' ] as $colour => $name ) {
      $points = [];
      for ( $y = 0; $y < $size; $y++ )
        for ( $x = 0; $x < $size; $x++ )
          if ( $board [$y] [$x] === $colour )
            $points [] = padGoName ( [ $x, $y ], $size );
      $parts [] = "$name: " . ( $points ? implode ( ', ', $points ) : 'no stones' );
    }

    if ( $moves ) {
      list ( $colour, $point, $number ) = end ( $moves );
      $parts [] = "Move $number, " . ( $colour == 'B' ? 'Black' : 'White' ) . ( $point === NULL ? ' passes' : ' at ' . padGoName ( $point, $size ) );
    }

    if ( $captured ['B'] or $captured ['W'] )
      $parts [] = "Captured: by Black {$captured ['B']}, by White {$captured ['W']}";

    return implode ( '. ', $parts ) . '.';

  }

?>
