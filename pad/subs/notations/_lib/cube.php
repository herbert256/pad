<?php

  // Cubes - the cube notation, {cube "R U R' U'"/}. A sequence of moves of a twisty cube written
  // in the notation of the World Cube Association's regulations (article 12a) - U D L R F B a
  // face turned a quarter clockwise as seen from that face, ' the other way, 2 a half turn, Rw
  // or r two layers, 3Rw three, M E S the middle slice, x y z the whole cube - applied to a
  // solved cube and drawn: its six faces unfolded as a net, or three of them as a cube seen from
  // above front right.
  //
  // padCubeMoves   the moves of a text: [ axis, layers, quarter turns ] each - parentheses group,
  //                a number after them repeats, (R U R' U')6 - or what it cannot read
  // padCubeSolved  a solved cube: every sticker [ position, normal, colour ] - a cubie's centre
  //                and the side of it the sticker is on, in the coordinates -(n-1) to n-1, step 2
  // padCubeTurn    a move applied: the stickers of the layers turned a quarter turn at a time
  // padCubeFaces   the stickers of each face in the order a net draws them
  // padCubeSvg     the net, or the cube seen from above
  //
  // The axes: x to the right, y up, z to the front. R turns the right layers about +x by -90
  // degrees - (x, y, z) to (x, z, -y), the front going up - U about +y, F about +z; L, D and B
  // the other way about the same axes. The colours are the scheme the WCA scrambles in: white
  // up, green front, red right, yellow down, blue back, orange left.
  //
  // Checked by what the notation's well-known sequences do: (R U R' U') six times brings the
  // cube back; M2 E2 S2 is the checkerboard, every face its own colour with the opposite face's
  // edges; the superflip U R2 F B R B2 R U2 L B2 R U' D' R2 F R' L B2 U2 F2 flips every edge in
  // place; the T permutation R U R' U' R' F R2 U' R' U' R U R' F' swaps the up-left and up-right
  // edges and the up-front-right and up-back-right corners.

  const PAD_CUBE_COLOURS = [ 'white'  => [ '#f8f8f6', '#f2f2ee' ], 'yellow' => [ '#ffd500', '#f2cb00' ],
                             'green'  => [ '#009b48', '#00a34d' ], 'blue'   => [ '#0046ad', '#2f6bd1' ],
                             'red'    => [ '#c41e3a', '#d8304c' ], 'orange' => [ '#ff5800', '#ff6d1f' ],
                             'edge'   => [ '#1f2328', '#0b0c0d' ] ];

  const PAD_CUBE_LONGEST = 2000;
  const PAD_CUBE_MOST    = 10000;

  // The faces: their axis (0 x, 1 y, 2 z), their side (+1, -1), their colour, their name.

  const PAD_CUBE_FACES = [ 'U' => [ 1,  1, 'white',  'up'    ], 'D' => [ 1, -1, 'yellow', 'down'  ],
                           'R' => [ 0,  1, 'red',    'right' ], 'L' => [ 0, -1, 'orange', 'left'  ],
                           'F' => [ 2,  1, 'green',  'front' ], 'B' => [ 2, -1, 'blue',   'back'  ] ];

  // A move: [ axis, the layers it turns as indexes 0 (the minus side) to n-1, quarter turns
  // about the plus axis, counterclockwise as seen from the plus side - 1, 2 or 3 ].

  function padCubeMoves ( $text, $layers ) {

    $text  = str_replace ( [ '’', '′' ], "'", (string) $text );
    $at    = 0;
    $moves = padCubeSequence ( $text, $at, $layers, 0 );

    if ( is_string ( $moves ) )
      return $moves;

    if ( $at < strlen ( $text ) )
      return "a ')' that opens no group";

    return $moves;

  }

  function padCubeSequence ( $text, &$at, $layers, $depth ) {

    if ( $depth > 20 )
      return "groups nested more than 20 deep";

    $moves  = [];
    $length = strlen ( $text );

    while ( $at < $length ) {

      if ( ctype_space ( $text [$at] ) ) {
        $at++;
        continue;
      }

      if ( $text [$at] == ')' )
        break;

      if ( $text [$at] == '(' ) {
        $at++;
        $inner = padCubeSequence ( $text, $at, $layers, $depth + 1 );
        if ( is_string ( $inner ) )
          return $inner;
        if ( $at >= $length or $text [$at] != ')' )
          return "a '(' that does not close";
        $at++;
        $times = 1;
        if ( preg_match ( '/\G(\d{1,3})(\'?)/', $text, $m, 0, $at ) ) {
          $times = (int) $m [1];
          $at   += strlen ( $m [0] );
          if ( $m [2] === "'" )
            $inner = padCubeInverse ( $inner );
        } elseif ( preg_match ( "/\G'/", $text, $m, 0, $at ) ) {
          $inner = padCubeInverse ( $inner );
          $at++;
        }
        if ( count ( $moves ) + $times * count ( $inner ) > PAD_CUBE_MOST )
          return "more than " . PAD_CUBE_MOST . " moves";
        for ( $i = 0; $i < $times; $i++ )
          array_push ( $moves, ...$inner );
        continue;
      }

      if ( ! preg_match ( "/\G(\d{1,2})?([URFDLB])(w)?(2'|2|')?|\G([urfdlb]|[MES]|[xyz])(2'|2|')?/", $text, $m, 0, $at ) )
        return "a move '" . padNotationShow ( substr ( $text, $at, 3 ) ) . "' - the moves are U D L R F B, Rw or r, 3Rw, M E S, x y z, each with ' or 2";

      $at += strlen ( $m [0] );
      $move = padCubeMove ( $m, $layers );

      if ( is_string ( $move ) )
        return $move;

      $moves [] = $move;

      if ( count ( $moves ) > PAD_CUBE_MOST )
        return "more than " . PAD_CUBE_MOST . " moves";

    }

    return $moves;

  }

  function padCubeMove ( $m, $layers ) {

    $amount = fn ( $suffix ) => [ '' => 1, "'" => 3, '2' => 2, "2'" => 2 ] [ $suffix ?? '' ];

    if ( ( $m [2] ?? '' ) !== '' ) {
      list ( $axis, $side ) = PAD_CUBE_FACES [ $m [2] ];
      $depth = ( $m [1] ?? '' ) !== '' ? (int) $m [1] : ( ( $m [3] ?? '' ) === 'w' ? 2 : 1 );
      if ( $depth < 1 or $depth > $layers )
        return "a move " . $m [0] . " of layer $depth - a cube of $layers layers has 1 to $layers";
      $wide  = ( ( $m [3] ?? '' ) === 'w' or ( $m [1] ?? '' ) === '' );
      $from  = $wide ? 1 : $depth;
      $which = [];
      for ( $layer = $from; $layer <= $depth; $layer++ )
        $which [] = ( $side > 0 ) ? $layers - $layer : $layer - 1;
      $turns = $amount ( $m [4] ?? '' );
      return [ $axis, $which, $side > 0 ? ( 4 - $turns ) % 4 : $turns ];
    }

    $letter = $m [5];
    $turns  = $amount ( $m [6] ?? '' );

    if ( ctype_lower ( $letter ) and $letter !== 'x' and $letter !== 'y' and $letter !== 'z' ) {
      list ( $axis, $side ) = PAD_CUBE_FACES [ strtoupper ( $letter ) ];
      if ( $layers < 3 )
        return "a move $letter - a cube of $layers layers has no wide moves";
      $which = ( $side > 0 ) ? [ $layers - 1, $layers - 2 ] : [ 0, 1 ];
      return [ $axis, $which, $side > 0 ? ( 4 - $turns ) % 4 : $turns ];
    }

    if ( in_array ( $letter, [ 'x', 'y', 'z' ] ) )
      return [ [ 'x' => 0, 'y' => 1, 'z' => 2 ] [$letter], range ( 0, $layers - 1 ), ( 4 - $turns ) % 4 ];

    // M turns as L does, E as D, S as F: the middle layer of a cube of odd layers.

    if ( $layers % 2 == 0 )
      return "a move $letter - a cube of $layers layers has no middle slice";

    list ( $axis, $as ) = [ 'M' => [ 0, -1 ], 'E' => [ 1, -1 ], 'S' => [ 2, 1 ] ] [$letter];

    return [ $axis, [ intdiv ( $layers, 2 ) ], $as > 0 ? ( 4 - $turns ) % 4 : $turns ];

  }

  // The moves undone: backwards, every turn the other way.

  function padCubeInverse ( $moves ) {

    return array_map ( fn ( $move ) => [ $move [0], $move [1], ( 4 - $move [2] ) % 4 ], array_reverse ( $moves ) );

  }

  function padCubeSolved ( $layers ) {

    $stickers = [];
    $edge     = $layers - 1;

    foreach ( PAD_CUBE_FACES as $face => list ( $axis, $side, $colour ) )
      for ( $a = -$edge; $a <= $edge; $a += 2 )
        for ( $b = -$edge; $b <= $edge; $b += 2 ) {
          $position = [ 0, 0, 0 ];
          $normal   = [ 0, 0, 0 ];
          $others   = array_values ( array_diff ( [ 0, 1, 2 ], [ $axis ] ) );
          $position [$axis]       = $side * $edge;
          $position [ $others [0] ] = $a;
          $position [ $others [1] ] = $b;
          $normal [$axis] = $side;
          $stickers [] = [ $position, $normal, $colour ];
        }

    return $stickers;

  }

  // A quarter turn counterclockwise about the plus axis, $turns times, of the stickers whose
  // cubies stand in the layers.

  function padCubeTurn ( $stickers, $move, $layers ) {

    list ( $axis, $which, $turns ) = $move;

    if ( ! $turns )
      return $stickers;

    $edge = $layers - 1;
    $in   = array_flip ( array_map ( fn ( $layer ) => 2 * $layer - $edge, $which ) );
    list ( $p, $q ) = [ [ 1, 2 ], [ 2, 0 ], [ 0, 1 ] ] [$axis];

    foreach ( $stickers as $at => list ( $position, $normal, $colour ) ) {
      if ( ! isset ( $in [ $position [$axis] ] ) )
        continue;
      for ( $i = 0; $i < $turns; $i++ ) {
        list ( $position [$p], $position [$q] ) = [ - $position [$q], $position [$p] ];
        list ( $normal [$p], $normal [$q] )     = [ - $normal [$q], $normal [$p] ];
      }
      $stickers [$at] = [ $position, $normal, $colour ];
    }

    return $stickers;

  }

  // The stickers of every face, row by row as the net shows it: the up face seen from above
  // with the back at its top, the four sides seen from outside with the up face above them, the
  // down face seen from below with the front at its top.

  function padCubeFaces ( $stickers, $layers ) {

    $edge  = $layers - 1;
    $faces = [];

    // Per face: the axis and side of its normal, then what runs down its rows and along its
    // columns - [ axis, sign ].

    $views = [ 'U' => [ 1,  1, [ 2,  1 ], [ 0,  1 ] ], 'L' => [ 0, -1, [ 1, -1 ], [ 2,  1 ] ],
               'F' => [ 2,  1, [ 1, -1 ], [ 0,  1 ] ], 'R' => [ 0,  1, [ 1, -1 ], [ 2, -1 ] ],
               'B' => [ 2, -1, [ 1, -1 ], [ 0, -1 ] ], 'D' => [ 1, -1, [ 2, -1 ], [ 0,  1 ] ] ];

    foreach ( $views as $face => list ( $axis, $side, $down, $across ) ) {
      $grid = array_fill ( 0, $layers, array_fill ( 0, $layers, '' ) );
      foreach ( $stickers as list ( $position, $normal, $colour ) ) {
        if ( $normal [$axis] != $side )
          continue;
        $row = intdiv ( $down [1] * $position [ $down [0] ] + $edge, 2 );
        $col = intdiv ( $across [1] * $position [ $across [0] ] + $edge, 2 );
        $grid [$row] [$col] = $colour;
      }
      $faces [$face] = $grid;
    }

    return $faces;

  }

  // The faces in words: solved, or each face row by row.

  function padCubeWords ( $faces ) {

    if ( ! array_filter ( $faces, fn ( $grid ) => count ( array_unique ( array_merge ( ...$grid ) ) ) > 1 ) )
      return 'Solved, every face one colour: ' . implode ( ', ', array_map ( fn ( $face ) => PAD_CUBE_FACES [$face] [3] . ' ' . $faces [$face] [0] [0], array_keys ( $faces ) ) ) . '.';

    $said = [];

    foreach ( $faces as $face => $grid )
      $said [] = ucfirst ( PAD_CUBE_FACES [$face] [3] ) . ': ' . implode ( '; ', array_map ( fn ( $row ) => implode ( ', ', $row ), $grid ) ) . '.';

    return implode ( ' ', $said );

  }

  // The net, a sticker $cell wide, or the cube seen from above front right: the up face a
  // diamond on top, the front face down the left, the right face down the right.

  function padCubeSvg ( $faces, $layers, $view, $size, $id, $title, $desc ) {

    $svg = '';
    $num = 'padNotationNum';

    if ( $view == 'net' ) {
      $cell   = 20;
      $face   = $cell * $layers + 4;
      $width  = 4 * $face + 2;
      $height = 3 * $face + 2;
      foreach ( [ 'U' => [ 1, 0 ], 'L' => [ 0, 1 ], 'F' => [ 1, 1 ], 'R' => [ 2, 1 ], 'B' => [ 3, 1 ], 'D' => [ 1, 2 ] ] as $name => list ( $fx, $fy ) ) {
        $x0   = 1 + $fx * $face;
        $y0   = 1 + $fy * $face;
        $svg .= '<rect class="pcb-face" x="' . $x0 . '" y="' . $y0 . '" width="' . $face . '" height="' . $face . '" rx="3"/>';
        foreach ( $faces [$name] as $row => $colours )
          foreach ( $colours as $col => $colour )
            $svg .= '<rect class="pcb-' . $colour . '" x="' . ( $x0 + 2 + $col * $cell + 1 ) . '" y="' . ( $y0 + 2 + $row * $cell + 1 ) . '" width="' . ( $cell - 2 ) . '" height="' . ( $cell - 2 ) . '" rx="2"/>';
      }
    } else {

      // Three faces in parallel projection: a unit along x goes right and down, along z left
      // and down, along y straight up.

      $unit   = 30;
      $ux     = [ $unit * 0.866, $unit * 0.5 ];
      $uz     = [ - $unit * 0.866, $unit * 0.5 ];
      $width  = (int) ceil ( 2 * $layers * $unit * 0.866 ) + 4;
      $height = (int) ceil ( 2 * $layers * $unit ) + 4;
      $origin = [ $width / 2, 2 ];
      $point  = fn ( $x, $y, $z ) => [ $origin [0] + $x * $ux [0] + $z * $uz [0], $origin [1] + $x * $ux [1] + $z * $uz [1] + ( $layers - $y ) * $unit ];
      $quad   = function ( $a, $b, $c, $d ) use ( $num ) {
        return 'M' . implode ( 'L', array_map ( fn ( $p ) => $num ( $p [0] ) . ' ' . $num ( $p [1] ), [ $a, $b, $c, $d ] ) ) . 'Z';
      };

      // Corners of the sticker at row, col of a face, inset a little.

      $sticker = function ( $face, $row, $col ) use ( $point, $layers ) {
        $in = 0.06;
        list ( $a0, $a1, $b0, $b1 ) = [ $row + $in, $row + 1 - $in, $col + $in, $col + 1 - $in ];
        switch ( $face ) {
          case 'U': $at = fn ( $r, $c ) => $point ( $c, $layers, $r ); break;
          case 'F': $at = fn ( $r, $c ) => $point ( $c, $layers - $r, $layers ); break;
          default:  $at = fn ( $r, $c ) => $point ( $layers, $layers - $r, $layers - $c ); break;
        }
        return [ $at ( $a0, $b0 ), $at ( $a0, $b1 ), $at ( $a1, $b1 ), $at ( $a1, $b0 ) ];
      };

      foreach ( [ 'U', 'F', 'R' ] as $name ) {
        $corners = $sticker ( $name, 0, 0 );
        $all     = [ $sticker ( $name, 0, 0 ) [0], $sticker ( $name, 0, $layers - 1 ) [1], $sticker ( $name, $layers - 1, $layers - 1 ) [2], $sticker ( $name, $layers - 1, 0 ) [3] ];
        $svg    .= '<path class="pcb-face" d="' . $quad ( ...$all ) . '"/>';
        foreach ( $faces [$name] as $row => $colours )
          foreach ( $colours as $col => $colour )
            $svg .= '<path class="pcb-' . $colour . '" d="' . $quad ( ...$sticker ( $name, $row, $col ) ) . '"/>';
      }

    }

    $scale = $size / $width;

    return '<svg xmlns="http://www.w3.org/2000/svg" class="pad-cube" role="img" aria-labelledby="' . "$id-title $id-desc" . '"'
         . ' width="' . $num ( $width * $scale ) . '" height="' . $num ( $height * $scale ) . '" viewBox="0 0 ' . $width . ' ' . $height . '">'
         . "<title id=\"$id-title\">" . padNotationAttr ( $title ) . '</title>'
         . "<desc id=\"$id-desc\">" . padNotationAttr ( $desc ) . '</desc>'
         . $svg . '</svg>';

  }

?>
