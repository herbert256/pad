<?php

  // Draughts - the draughts notation, {draughts 'W:W31-50:B1-20'/}. A draughts position written
  // as the FEN of PDN, the Portable Draughts Notation, drawn as an SVG board: international
  // draughts on 10 by 10, or the 8 by 8 board of English draughts and checkers.
  //
  // padDraughtsFen  the position of a FEN - the side to move, then each colour with its
  //                 squares, a K before a king's: W:W31,32,K33:B1,2 - and ranges 31-35; the
  //                 reason it is none as a string
  // padDraughtsAt   the row and the column of a numbered square
  // padDraughtsSvg  the board
  //
  // The squares are numbered as both games number them: the playing squares only, the dark
  // ones, row by row from Black's side at the top, left to right - 1 to 50 on the large board,
  // 1 to 32 on the small - so the corner square at the bottom left of White's side is dark,
  // 46 or 29. White plays up from the bottom, Black down from the top.

  const PAD_DRAUGHTS_COLOURS = [ 'light' => [ '#f2e3c6', '#bfae8e' ], 'dark'  => [ '#9a6a42', '#6f4b2e' ],
                                 'white' => [ '#fbfaf5', '#ecebe4' ], 'black' => [ '#2a2522', '#1a1715' ],
                                 'ink'   => [ '#1a1715', '#0d0b0a' ], 'mark'  => [ 'rgba(235,200,40,.6)', 'rgba(235,200,40,.5)' ],
                                 'text'  => [ '#f5ead6', '#e6d7bc' ], 'crown' => [ '#c99a1c', '#e0b437' ] ];

  function padDraughtsFen ( $fen, $board ) {

    $squares = ( $board == 10 ) ? 50 : 32;
    $fen     = trim ( trim ( (string) $fen ), '"' );
    $fen     = rtrim ( trim ( $fen ), '.' );
    $fields  = array_map ( 'trim', explode ( ':', $fen ) );
    $side    = strtoupper ( array_shift ( $fields ) );

    if ( $side !== 'W' and $side !== 'B' )
      return "the side to move is '" . padNotationShow ( $side ) . "', not W or B";

    $pieces = [];

    foreach ( $fields as $field ) {

      if ( $field === '' )
        continue;

      $colour = strtoupper ( $field [0] );

      if ( $colour !== 'W' and $colour !== 'B' )
        return "a part '" . padNotationShow ( $field ) . "' starts with no W or B";

      foreach ( explode ( ',', substr ( $field, 1 ) ) as $item ) {

        $item = trim ( $item );

        if ( $item === '' )
          continue;

        $king = ( strtoupper ( $item [0] ) === 'K' );

        if ( $king )
          $item = substr ( $item, 1 );

        if ( ! preg_match ( '/^(\d+)(?:-(\d+))?$/D', $item, $m ) )
          return "it has a square '" . padNotationShow ( $item ) . "' that is no number - squares are 1 to $squares, a K before a king";

        $to = isset ( $m [2] ) ? (int) $m [2] : (int) $m [1];

        if ( (int) $m [1] > $to )
          return "it has a range " . padNotationShow ( $item ) . " that runs backwards";

        for ( $square = (int) $m [1]; $square <= $to; $square++ ) {
          if ( $square < 1 or $square > $squares )
            return "it has a square $square off the board of $board by $board, which has 1 to $squares";
          if ( isset ( $pieces [$square] ) )
            return "it has two pieces on square $square";
          $pieces [$square] = $king ? $colour . 'K' : $colour;
        }

      }

    }

    ksort ( $pieces );

    return [ $side, $pieces ];

  }

  // The row from the top and the column from the left of a numbered square: a row holds half
  // the board's squares; on the even rows, counted from 0, the playing squares are the odd
  // columns.

  function padDraughtsAt ( $square, $board ) {

    $half = $board / 2;
    $row  = intdiv ( $square - 1, $half );
    $col  = ( ( $square - 1 ) % $half ) * 2 + ( $row % 2 == 0 ? 1 : 0 );

    return [ $row, $col ];

  }

  // The board, 80 units a square; a man a disc with a ring, a king the same with a crown on
  // it; the numbers of the squares in their corner with $numbers; marked squares tinted.
  // Flipped, Black is at the bottom.

  function padDraughtsSvg ( $position, $board, $flip, $numbers, $marks, $pixels, $id, $title, $desc ) {

    list ( $side, $pieces ) = $position;

    $cell = 80;
    $full = $board * $cell;
    $at   = function ( $square ) use ( $board, $flip, $cell ) {
      list ( $row, $col ) = padDraughtsAt ( $square, $board );
      if ( $flip ) {
        $row = $board - 1 - $row;
        $col = $board - 1 - $col;
      }
      return [ $col * $cell, $row * $cell ];
    };

    $dark = '';
    $text = '';

    for ( $square = 1; $square <= $board * $board / 2; $square++ ) {
      list ( $x, $y ) = $at ( $square );
      $dark .= "M$x {$y}h$cell" . "v$cell" . "h-$cell" . 'z';
      if ( $numbers )
        $text .= '<text class="pdr-num" x="' . ( $x + 5 ) . '" y="' . ( $y + 21 ) . '">' . $square . '</text>';
    }

    $svg = '<rect class="pdr-light" x="0" y="0" width="' . $full . '" height="' . $full . '"/>'
         . '<path class="pdr-dark" d="' . $dark . '"/>';

    foreach ( $marks as $square ) {
      list ( $x, $y ) = $at ( $square );
      $svg .= '<rect class="pdr-mark" x="' . $x . '" y="' . $y . '" width="' . $cell . '" height="' . $cell . '"/>';
    }

    $svg .= $text;

    foreach ( $pieces as $square => $piece ) {

      list ( $x, $y ) = $at ( $square );

      $cx    = $x + $cell / 2;
      $cy    = $y + $cell / 2;
      $class = ( $piece [0] == 'W' ) ? 'pdr-w' : 'pdr-b';

      $svg .= '<g class="' . $class . '">'
            . '<circle cx="' . $cx . '" cy="' . ( $cy + 3 ) . '" r="31"/>'
            . '<circle cx="' . $cx . '" cy="' . ( $cy - 2 ) . '" r="31"/>'
            . '<circle class="pdr-ring" cx="' . $cx . '" cy="' . ( $cy - 2 ) . '" r="20"/>';

      if ( strlen ( $piece ) > 1 )
        $svg .= '<path class="pdr-crown" d="M' . ( $cx - 15 ) . ' ' . ( $cy + 6 ) . 'l-3 -17 9 8 9 -13 9 13 9 -8 -3 17z"/>';

      $svg .= '</g>';

    }

    $svg .= '<rect class="pdr-edge" x="1" y="1" width="' . ( $full - 2 ) . '" height="' . ( $full - 2 ) . '"/>';

    return '<svg xmlns="http://www.w3.org/2000/svg" class="pad-draughts" role="img" aria-labelledby="' . "$id-title $id-desc" . '"'
         . ' width="' . $pixels . '" height="' . $pixels . '" viewBox="0 0 ' . $full . ' ' . $full . '">'
         . "<title id=\"$id-title\">" . padNotationAttr ( $title ) . '</title>'
         . "<desc id=\"$id-desc\">" . padNotationAttr ( $desc ) . '</desc>'
         . $svg . '</svg>';

  }

  // The position in words: each colour's men and kings by their squares, the side to move,
  // the marked squares.

  function padDraughtsDescribe ( $position, $marks ) {

    list ( $side, $pieces ) = $position;

    $parts = [];

    foreach ( [ 'W' => 'White', 'B' => 'Black' ] as $colour => $name ) {
      $men   = array_keys ( $pieces, $colour, TRUE );
      $kings = array_keys ( $pieces, $colour . 'K', TRUE );
      $list  = [];
      if ( $men )
        $list [] = ( count ( $men ) == 1 ? 'man ' : 'men ' ) . implode ( ', ', $men );
      if ( $kings )
        $list [] = ( count ( $kings ) == 1 ? 'king ' : 'kings ' ) . implode ( ', ', $kings );
      $parts [] = "$name: " . ( $list ? implode ( '; ', $list ) : 'no pieces' );
    }

    $parts [] = ( $side == 'W' ? 'White' : 'Black' ) . ' to move';

    if ( $marks )
      $parts [] = 'Marked ' . implode ( ', ', $marks );

    return implode ( '. ', $parts ) . '.';

  }

?>
