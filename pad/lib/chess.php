<?php

  // Chess diagrams - the {chess} tag. A position written as FEN, the notation every chess
  // program reads and writes, drawn as inline SVG: the board, the pieces as shapes of PAD's
  // own (no font, no image), the files and ranks along the edge, marked squares and arrows.
  //
  //   {chess 'rnbqkbnr/pppppppp/8/8/4P3/8/PPPP1PPP/RNBQKBNR b KQkq e3 0 1'}
  //   {chess $fen, flip, highlight='e2,e4', arrow='g1-f3, f1-c4', size=320}
  //
  // padChessFen     the position of a FEN: [ squares, side, castling, en passant ], or the
  //                 reason it is no FEN as a string
  // padChess        the SVG of a position
  // padChessSquare  [ file, rank ] of a square name - 'e4' is [ 4, 4 ] - or NULL
  //
  // The colours are custom properties on .pad-chess with light-dark() defaults: the squares
  // (--pad-chess-light, --pad-chess-dark), the pieces (--pad-chess-white, --pad-chess-black,
  // --pad-chess-ink), the marked squares (--pad-chess-mark), the arrows (--pad-chess-arrow)
  // and the coordinates (--pad-chess-text). The style is written with the first board of a
  // page only.

  const PAD_CHESS_NAMES = [ 'k' => 'king', 'q' => 'queen', 'r' => 'rook', 'b' => 'bishop', 'n' => 'knight', 'p' => 'pawn' ];

  // The pieces in a square of 100 units, base at the bottom: the outline in the piece's own
  // colour, the details (d) in the colour of the other side so they show on both.

  const PAD_CHESS_SHAPES = [
    'p' => [ '<path d="M50 17a12 12 0 0 1 8.5 20.5c4 2 6.5 5 6.5 8.5h-30c0-3.5 2.5-6.5 6.5-8.5A12 12 0 0 1 50 17zM39 46h22c0 9 4 19 12 28H27c8-9 12-19 12-28z"/>',
             '<rect x="24" y="74" width="52" height="11" rx="3"/>' ],
    'r' => [ '<path d="M27 15h10v8h7v-8h12v8h7v-8h10v20l-7 6v27h7v8H27v-8h7V41l-7-6z"/>',
             '<rect x="22" y="76" width="56" height="10" rx="3"/>',
             '<path class="pcs-d" fill="none" d="M34 41h32M34 68h32"/>' ],
    'n' => [ '<path d="M30 76c0-15 9-22 15-29-6 3-12 5-18 9-5-2-7-7-4-12 8-10 14-19 18-27l-1-8 8 6c18 0 30 15 28 40l-2 21z"/>',
             '<rect x="24" y="76" width="52" height="10" rx="3"/>',
             '<circle class="pcs-e" cx="45" cy="28" r="3"/>',
             '<path class="pcs-d" fill="none" d="M27 44c2 2 4 3 7 2"/>' ],
    'b' => [ '<circle cx="50" cy="12" r="5"/>',
             '<path d="M50 17c13 7 17 19 11 29H39c-6-10-2-22 11-29zM40 50h20c0 10 4 18 10 25H30c6-7 10-15 10-25z"/>',
             '<rect x="36" y="45" width="28" height="7" rx="3"/>',
             '<rect x="24" y="75" width="52" height="11" rx="3"/>',
             '<path class="pcs-d" fill="none" d="M56 25l-10 12"/>' ],
    'q' => [ '<path d="M28 74l-8-42 15 20 2-27 10 24 3-28 3 28 10-24 2 27 15-20-8 42z"/>',
             '<circle cx="20" cy="29" r="5"/><circle cx="37" cy="22" r="5"/><circle cx="50" cy="18" r="5"/><circle cx="63" cy="22" r="5"/><circle cx="80" cy="29" r="5"/>',
             '<rect x="22" y="74" width="56" height="12" rx="3"/>',
             '<path class="pcs-d" fill="none" d="M30 66h40"/>' ],
    'k' => [ '<path d="M47 8h6v6h6v6h-6v9h-6v-9h-6v-6h6z"/>',
             '<path d="M50 30c5 0 8 4 8 9 0 4-3 8-8 15-5-7-8-11-8-15 0-5 3-9 8-9z"/>',
             '<path d="M50 54c4-8 10-14 19-14 10 0 14 9 11 18-2 6-6 11-8 16H28c-2-5-6-10-8-16-3-9 1-18 11-18 9 0 15 6 19 14z"/>',
             '<rect x="22" y="74" width="56" height="12" rx="3"/>',
             '<path class="pcs-d" fill="none" d="M50 54v18M30 66h40"/>' ] ];

  function padChessAttr ( $text ) {

    return htmlspecialchars ( (string) $text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );

  }

  // The position of a FEN. Only the placement is needed; the side to move, castling, en
  // passant square and the two counters are checked when they are there.

  function padChessFen ( $fen ) {

    $fields = preg_split ( '/\s+/', trim ( (string) $fen ) );

    if ( count ( $fields ) > 6 )
      return 'it has more than six fields';

    $ranks = explode ( '/', $fields [0] );

    if ( count ( $ranks ) != 8 )
      return 'the placement has ' . count ( $ranks ) . ' ranks, not 8';

    $squares = [];

    foreach ( $ranks as $index => $text ) {

      $rank = 8 - $index;
      $file = 0;

      foreach ( str_split ( $text ) as $char ) {
        if ( $char >= '1' and $char <= '8' )
          $file += (int) $char;
        elseif ( isset ( PAD_CHESS_NAMES [ strtolower ( $char ) ] ) ) {
          if ( $file < 8 )
            $squares [ "$file,$rank" ] = $char;
          $file++;
        } else
          return "the placement has a '" . $char . "' - pieces are KQRBNP and kqrbnp, empty squares a digit";
      }

      if ( $file != 8 )
        return "rank $rank has $file squares, not 8";

    }

    $side     = $fields [1] ?? 'w';
    $castling = $fields [2] ?? '-';
    $passant  = $fields [3] ?? '-';

    if ( $side !== 'w' and $side !== 'b' )
      return "the side to move is '$side', not w or b";

    if ( $castling !== '-' and ! preg_match ( '/^K?Q?k?q?$/', $castling ) or $castling === '' )
      return "the castling rights are '$castling', not - or a part of KQkq";

    if ( $passant !== '-' and ! preg_match ( '/^[a-h][36]$/', $passant ) )
      return "the en passant square is '$passant', not - or a square on rank 3 or 6";

    foreach ( [ 4, 5 ] as $at )
      if ( isset ( $fields [$at] ) and ! ctype_digit ( $fields [$at] ) )
        return "the " . ( $at == 4 ? 'halfmove clock' : 'move number' ) . " is '{$fields [$at]}', not a number";

    return [ $squares, $side, $castling, $passant ];

  }

  function padChessSquare ( $name ) {

    $name = strtolower ( trim ( (string) $name ) );

    if ( ! preg_match ( '/^([a-h])([1-8])$/', $name, $m ) )
      return NULL;

    return [ ord ( $m [1] ) - 97, (int) $m [2] ];

  }

  // The board. $marks are squares [ file, rank ], $arrows pairs of them. The board is 800
  // units with the coordinates in a margin left and below; a flipped board has Black at
  // the bottom.

  function padChess ( $position, $flip, $marks, $arrows, $size, $title ) {

    list ( $squares, $side, $castling, $passant ) = $position;

    $at = fn ( $file, $rank ) => $flip ? [ 30 + ( 7 - $file ) * 100, 4 + ( $rank - 1 ) * 100 ]
                                       : [ 30 + $file * 100, 4 + ( 8 - $rank ) * 100 ];

    $id    = padChessId ( func_get_args () );
    $title = ( $title !== '' ) ? $title : 'Chess position, ' . ( $side == 'w' ? 'White' : 'Black' ) . ' to move';

    $svg = '<svg xmlns="http://www.w3.org/2000/svg" class="pad-chess" role="img" aria-labelledby="' . "$id-title $id-desc" . '"'
         . ' width="' . (int) $size . '" height="' . (int) $size . '" viewBox="0 0 834 834">'
         . "<title id=\"$id-title\">" . padChessAttr ( $title ) . '</title>'
         . "<desc id=\"$id-desc\">" . padChessAttr ( padChessDescribe ( $squares, $side, $castling, $passant, $marks, $arrows ) ) . '</desc>'
         . padChessStyle ()
         . '<rect class="pcs-light" x="30" y="4" width="800" height="800"/>';

    // The dark squares as one path, then the marked squares over them.

    $dark = '';

    for ( $rank = 1; $rank <= 8; $rank++ )
      for ( $file = 0; $file < 8; $file++ )
        if ( ( $file + $rank ) % 2 == 1 ) {
          list ( $x, $y ) = $at ( $file, $rank );
          $dark .= "M$x {$y}h100v100h-100z";
        }

    $svg .= "<path class=\"pcs-dark\" d=\"$dark\"/>";

    foreach ( $marks as list ( $file, $rank ) ) {
      list ( $x, $y ) = $at ( $file, $rank );
      $svg .= "<rect class=\"pcs-mark\" x=\"$x\" y=\"$y\" width=\"100\" height=\"100\"/>";
    }

    // The coordinates: the files under the board, the ranks to the left of it.

    for ( $i = 0; $i < 8; $i++ ) {
      list ( $x, $y ) = $at ( $i, $i + 1 );
      $svg .= '<text class="pcs-coord" x="' . ( $x + 50 ) . '" y="828" text-anchor="middle">' . chr ( 97 + $i ) . '</text>'
            . '<text class="pcs-coord" x="15" y="' . ( $y + 58 ) . '" text-anchor="middle">' . ( $i + 1 ) . '</text>';
    }

    $svg .= '<rect class="pcs-edge" x="30" y="4" width="800" height="800"/>';

    // The pieces, in the order of the FEN: rank 8 to rank 1, a to h.

    foreach ( $squares as $key => $piece ) {
      list ( $file, $rank ) = explode ( ',', $key );
      list ( $x, $y )       = $at ( (int) $file, (int) $rank );
      $svg .= "<g class=\"pcs-" . ( ctype_upper ( $piece ) ? 'w' : 'b' ) . "\" transform=\"translate($x $y)\">"
            . implode ( '', PAD_CHESS_SHAPES [ strtolower ( $piece ) ] ) . '</g>';
    }

    foreach ( $arrows as list ( $from, $to ) )
      $svg .= padChessArrow ( $at ( ...$from ), $at ( ...$to ) );

    return "$svg</svg>";

  }

  // An arrow from the middle of one square to the middle of another, as one shape: a shaft
  // of 16 units and a head of 44 wide and 38 long, its point short of the middle.

  function padChessArrow ( $from, $to ) {

    $x1 = $from [0] + 50;
    $y1 = $from [1] + 50;
    $x2 = $to   [0] + 50;
    $y2 = $to   [1] + 50;
    $dx = $x2 - $x1;
    $dy = $y2 - $y1;
    $l  = sqrt ( $dx * $dx + $dy * $dy );

    if ( $l == 0 )
      return '';

    $ux = $dx / $l;
    $uy = $dy / $l;
    $px = -$uy;
    $py = $ux;
    $tip  = $l - 12;
    $neck = $tip - 38;
    $pt   = fn ( $along, $side ) => padChessXY ( $x1 + $ux * $along + $px * $side ) . ',' . padChessXY ( $y1 + $uy * $along + $py * $side );

    return '<polygon class="pcs-arrow" points="' . implode ( ' ', [ $pt ( 0, 8 ), $pt ( $neck, 8 ), $pt ( $neck, 22 ), $pt ( $tip, 0 ),
                                                                     $pt ( $neck, -22 ), $pt ( $neck, -8 ), $pt ( 0, -8 ) ] ) . '"/>';

  }

  function padChessXY ( $n ) {

    return rtrim ( rtrim ( number_format ( $n, 1, '.', '' ), '0' ), '.' );

  }

  // The text a screen reader gets: every piece by side and kind, the side to move, and the
  // castling rights, en passant square, marks and arrows when there are any.

  function padChessDescribe ( $squares, $side, $castling, $passant, $marks, $arrows ) {

    $name  = fn ( $f, $r ) => chr ( 97 + $f ) . $r;
    $parts = [];

    foreach ( [ 'White' => TRUE, 'Black' => FALSE ] as $colour => $upper ) {

      $kinds = [];

      foreach ( $squares as $key => $piece )
        if ( ctype_upper ( $piece ) == $upper ) {
          list ( $file, $rank ) = explode ( ',', $key );
          $kinds [ strtolower ( $piece ) ] [] = $name ( (int) $file, (int) $rank );
        }

      $list = [];

      foreach ( PAD_CHESS_NAMES as $letter => $kind )
        if ( isset ( $kinds [$letter] ) ) {
          sort ( $kinds [$letter] );
          $list [] = $kind . ( count ( $kinds [$letter] ) > 1 ? 's ' : ' ' ) . implode ( ', ', $kinds [$letter] );
        }

      $parts [] = "$colour: " . ( $list ? implode ( '; ', $list ) : 'no pieces' );

    }

    $parts [] = ( $side == 'w' ? 'White' : 'Black' ) . ' to move';

    if ( $castling !== '-' )
      $parts [] = "castling $castling";

    if ( $passant !== '-' )
      $parts [] = "en passant $passant";

    if ( $marks )
      $parts [] = 'marked ' . implode ( ', ', array_map ( fn ( $s ) => $name ( ...$s ), $marks ) );

    foreach ( $arrows as list ( $from, $to ) )
      $parts [] = 'arrow ' . $name ( ...$from ) . ' to ' . $name ( ...$to );

    return implode ( '. ', $parts ) . '.';

  }

  // The id of a board, made from the board itself as {chart} makes its own: two boards on
  // a page never share one, and the same board drawn twice gets a number behind the second.

  function padChessId ( $board ) {

    static $drawn = [];

    $id = 'pad-chess-' . substr ( md5 ( serialize ( $board ) ), 0, 8 );

    $drawn [$id] = ( $drawn [$id] ?? 0 ) + 1;

    return ( $drawn [$id] > 1 ) ? $id . '-' . $drawn [$id] : $id;

  }

  // The look, written once per page: every colour a custom property with a light-dark()
  // default, the light steps alone for a browser without light-dark().

  function padChessStyle () {

    static $written = FALSE;

    if ( $written )
      return '';

    $written = TRUE;

    $roles = [ 'light' => [ '#f0d9b5', '#c9b38f' ], 'dark'  => [ '#b58863', '#8b6748' ],
               'white' => [ '#ffffff', '#f4f3ee' ], 'black' => [ '#2b2a28', '#22211f' ],
               'ink'   => [ '#1a1a19', '#0e0e0d' ], 'mark'  => [ 'rgba(235,200,40,.55)', 'rgba(235,200,40,.5)' ],
               'arrow' => [ 'rgba(21,120,27,.75)', 'rgba(60,170,70,.8)' ], 'text'  => [ '#52514e', '#c3c2b7' ] ];

    $light = $both = '';

    foreach ( $roles as $role => list ( $day, $night ) ) {
      $light .= "--pad-chess-$role:$day;";
      $both  .= "--pad-chess-$role:light-dark($day,$night);";
    }

    return '<style>'
         . ":where(.pad-chess){{$light}}"
         . "@supports (color:light-dark(#000,#fff)){:where(.pad-chess){{$both}}}"
         . ':where(.pad-chess){max-width:100%;max-height:100%;height:auto}'
         . '.pad-chess .pcs-light{fill:var(--pad-chess-light)}'
         . '.pad-chess .pcs-dark{fill:var(--pad-chess-dark)}'
         . '.pad-chess .pcs-mark{fill:var(--pad-chess-mark)}'
         . '.pad-chess .pcs-edge{fill:none;stroke:var(--pad-chess-dark);stroke-width:2}'
         . '.pad-chess .pcs-coord{fill:var(--pad-chess-text);font:600 26px system-ui,sans-serif}'
         . '.pad-chess .pcs-w,.pad-chess .pcs-b{stroke:var(--pad-chess-ink);stroke-width:3;stroke-linejoin:round;stroke-linecap:round}'
         . '.pad-chess .pcs-w{fill:var(--pad-chess-white)}'
         . '.pad-chess .pcs-b{fill:var(--pad-chess-black)}'
         . '.pad-chess .pcs-w .pcs-d{stroke:var(--pad-chess-ink)}'
         . '.pad-chess .pcs-b .pcs-d{stroke:var(--pad-chess-white)}'
         . '.pad-chess .pcs-w .pcs-e{fill:var(--pad-chess-ink);stroke:none}'
         . '.pad-chess .pcs-b .pcs-e{fill:var(--pad-chess-white);stroke:none}'
         . '.pad-chess .pcs-arrow{fill:var(--pad-chess-arrow)}'
         . '</style>';

  }

?>
