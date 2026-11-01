<?php

  // Semaphore - the semaphore notation, {semaphore 'PAD'/}. A text in flag semaphore: a figure
  // per letter, holding two flags at two of eight positions around the body.
  //
  // The positions are counted as a clock face seen by the reader, from straight down: 0 down,
  // 1 low to the reader's left (7:30), 2 out to the left, 3 high left, 4 straight up, 5 high
  // right, 6 out to the right, 7 low right. A letter is two positions - the first circle A to
  // G is one arm down and the other going round from 1 to 7 (D straight up), the second circle
  // H to N starts at 1, and so on; J and V break the pattern, as the code has them. A digit is
  // the letter A to I for 1 to 9 and K for 0, after the numeral sign (4, 5); the letters after
  // digits take J, the letters sign. The flags at sea are red and yellow, divided diagonally.
  //
  // padSemaphorePairs  the figures of a text: [ char, [ position, position ] ] per figure, a
  //                    word apart, and the characters it has no figure for
  // padSemaphoreSvg    the figures drawn in a row, their letter under each

  const PAD_SEMAPHORE_LETTERS = [
    'A' => [ 0, 1 ], 'B' => [ 0, 2 ], 'C' => [ 0, 3 ], 'D' => [ 0, 4 ], 'E' => [ 0, 5 ], 'F' => [ 0, 6 ], 'G' => [ 0, 7 ],
    'H' => [ 1, 2 ], 'I' => [ 1, 3 ], 'K' => [ 1, 4 ], 'L' => [ 1, 5 ], 'M' => [ 1, 6 ], 'N' => [ 1, 7 ],
    'O' => [ 2, 3 ], 'P' => [ 2, 4 ], 'Q' => [ 2, 5 ], 'R' => [ 2, 6 ], 'S' => [ 2, 7 ],
    'T' => [ 3, 4 ], 'U' => [ 3, 5 ], 'Y' => [ 3, 6 ],
    'J' => [ 4, 6 ], 'V' => [ 4, 7 ],
    'W' => [ 5, 6 ], 'X' => [ 5, 7 ],
    'Z' => [ 6, 7 ] ];

  const PAD_SEMAPHORE_NUMERALS = [ 4, 5 ];
  const PAD_SEMAPHORE_DIGITS   = [ '1' => 'A', '2' => 'B', '3' => 'C', '4' => 'D', '5' => 'E', '6' => 'F',
                                   '7' => 'G', '8' => 'H', '9' => 'I', '0' => 'K' ];

  const PAD_SEMAPHORE_COLOURS = [ 'figure' => [ '#374151', '#d1d5db' ], 'text' => [ '#4b5563', '#9ca3af' ] ];

  // The figures of a text, a list of words; in a word each figure is [ what it shows, its two
  // positions ] - the numeral sign shows '#', the letters sign 'J'.

  function padSemaphorePairs ( $text ) {

    $words   = [];
    $unknown = [];
    $numeric = FALSE;

    foreach ( preg_split ( '/\s+/u', trim ( (string) $text ) ) as $word ) {

      $figures = [];

      foreach ( preg_split ( '//u', $word, -1, PREG_SPLIT_NO_EMPTY ) as $char ) {

        $upper = strtoupper ( $char );

        if ( isset ( PAD_SEMAPHORE_DIGITS [$char] ) ) {
          if ( ! $numeric )
            $figures [] = [ '#', PAD_SEMAPHORE_NUMERALS ];
          $numeric    = TRUE;
          $figures [] = [ $char, PAD_SEMAPHORE_LETTERS [ PAD_SEMAPHORE_DIGITS [$char] ] ];
        } elseif ( isset ( PAD_SEMAPHORE_LETTERS [$upper] ) ) {
          if ( $numeric )
            $figures [] = [ 'J', PAD_SEMAPHORE_LETTERS ['J'] ];
          $numeric    = FALSE;
          $figures [] = [ $upper, PAD_SEMAPHORE_LETTERS [$upper] ];
        } else
          $unknown [$char] = TRUE;

      }

      if ( $figures )
        $words [] = $figures;

    }

    return [ $words, array_keys ( $unknown ) ];

  }

  // Which arm holds which position. The positions on the reader's left (1 to 3) are held by
  // the signaller's right arm, which hangs at the reader's left, those on the right (5 to 7)
  // by the left arm; down and up by whichever is free. Two positions on one side: the arm of
  // that side takes the one nearer straight up, the other arm reaches across the body for the
  // lower one - as the charts of the code draw H, I, O, W, X and Z. Answers [ position of the
  // arm at the reader's left, position of the arm at the reader's right ].

  function padSemaphoreArms ( $pair ) {

    list ( $a, $b ) = $pair;

    $side = fn ( $p ) => ( $p >= 1 and $p <= 3 ) ? 'L' : ( ( $p >= 5 and $p <= 7 ) ? 'R' : '' );
    $up   = fn ( $p ) => abs ( 4 - $p );

    if ( $side ( $a ) == 'L' and $side ( $b ) == 'L' )
      return ( $up ( $a ) <= $up ( $b ) ) ? [ $a, $b ] : [ $b, $a ];

    if ( $side ( $a ) == 'R' and $side ( $b ) == 'R' )
      return ( $up ( $a ) <= $up ( $b ) ) ? [ $b, $a ] : [ $a, $b ];

    if ( $side ( $a ) == 'L' or $side ( $b ) == 'R' )
      return [ $a, $b ];

    if ( $side ( $b ) == 'L' or $side ( $a ) == 'R' )
      return [ $b, $a ];

    // Down and up - D, and the digit 4: the arm at the reader's left goes on round from A, B
    // and C to straight up, and the other hangs down, as the chart of the code has it, seen
    // facing the signaller. Up went to the arm at the reader's right.

    if ( $a == 4 or $b == 4 )
      return [ 4, ( $a == 4 ) ? $b : $a ];

    return [ $a, $b ];

  }

  // One figure on a box of 80 by 100: the head, the body, the legs, and two arms - each a line
  // from its shoulder in the direction of its position, a staff beyond the hand and a square
  // flag on it, red and yellow divided along its diagonal, the red away from the staff.

  function padSemaphoreFigure ( $pair, $x, $y ) {

    list ( $left, $right ) = padSemaphoreArms ( $pair );

    $num = fn ( $n ) => padNotationNum ( $n );

    $svg = '<circle class="psm-body" cx="' . ( $x + 40 ) . '" cy="' . ( $y + 30 ) . '" r="7"/>'
         . '<path class="psm-body" d="M' . ( $x + 34 ) . ' ' . ( $y + 40 ) . 'h12l2 28h-16z"/>'
         . '<path class="psm-limb" d="M' . ( $x + 37 ) . ' ' . ( $y + 67 ) . 'l-3 24M' . ( $x + 43 ) . ' ' . ( $y + 67 ) . 'l3 24"/>';

    foreach ( [ [ $left, $x + 35 ], [ $right, $x + 45 ] ] as list ( $position, $shoulder ) ) {

      // Clockwise from straight down, as the reader sees it: x grows to the right, y down.

      $angle = deg2rad ( 45 * $position );
      $dx    = - sin ( $angle );
      $dy    = cos ( $angle );
      $sy    = $y + 43;

      $hx = $shoulder + 19 * $dx;
      $hy = $sy + 19 * $dy;
      $ex = $shoulder + 34 * $dx;
      $ey = $sy + 34 * $dy;

      // The flag: a square of 13 with one side along the end of the staff - hanging under a
      // staff that leans, out to the side of a staff that stands up or hangs down.

      $px = - $dy;
      $py = $dx;

      if ( abs ( $dx ) > 0.3 ? $py < 0 : $px * ( $shoulder - $x - 40 ) < 0 ) {
        $px = - $px;
        $py = - $py;
      }
      $fx = $shoulder + 21 * $dx;
      $fy = $sy + 21 * $dy;

      $corner = [ [ $fx, $fy ], [ $ex, $ey ], [ $ex + 13 * $px, $ey + 13 * $py ], [ $fx + 13 * $px, $fy + 13 * $py ] ];
      $point  = fn ( $i ) => $num ( $corner [$i] [0] ) . ',' . $num ( $corner [$i] [1] );

      $svg .= '<path class="psm-limb" d="M' . $num ( $shoulder ) . ' ' . $sy . 'L' . $num ( $hx ) . ' ' . $num ( $hy ) . '"/>'
            . '<path class="psm-staff" d="M' . $num ( $hx ) . ' ' . $num ( $hy ) . 'L' . $num ( $ex ) . ' ' . $num ( $ey ) . '"/>'
            . '<polygon class="psm-yellow" points="' . $point ( 0 ) . ' ' . $point ( 1 ) . ' ' . $point ( 3 ) . '"/>'
            . '<polygon class="psm-red" points="' . $point ( 1 ) . ' ' . $point ( 2 ) . ' ' . $point ( 3 ) . '"/>';

    }

    return $svg;

  }

  // The figures in rows: a figure 80 across, a word 30 more; a row breaks between words after
  // 720 units.

  function padSemaphoreSvg ( $words, $size, $id, $title ) {

    $rows = [ [] ];
    $used = 0;

    foreach ( $words as $word ) {
      $length = count ( $word ) * 80;
      if ( $rows [ count ( $rows ) - 1 ] and $used + 30 + $length > 720 ) {
        $rows [] = [];
        $used    = 0;
      }
      $used += ( $rows [ count ( $rows ) - 1 ] ? 30 : 0 ) + $length;
      $rows [ count ( $rows ) - 1 ] [] = $word;
    }

    $rowH   = 120;
    $body   = '';
    $widest = 0;
    $said   = [];

    foreach ( $rows as $row => $line ) {

      $x = 0;

      foreach ( $line as $index => $word ) {

        if ( $index )
          $x += 30;

        foreach ( $word as list ( $char, $pair ) ) {

          $body .= '<g><title>' . padNotationAttr ( $char == '#' ? 'the numeral sign' : $char ) . '</title>'
                 . padSemaphoreFigure ( $pair, $x, $row * $rowH )
                 . '<text class="psm-char" x="' . ( $x + 40 ) . '" y="' . ( $row * $rowH + 112 ) . '" text-anchor="middle">' . padNotationAttr ( $char ) . '</text></g>';

          $said [] = $char;
          $x      += 80;

        }

      }

      $widest = max ( $widest, $x );

    }

    $height = count ( $rows ) * $rowH;
    $scale  = $size / 80;
    $desc   = 'Flag semaphore, a figure per sign: ' . implode ( ', ', array_map ( fn ( $one ) => $one == '#' ? 'numeral sign' : $one, $said ) ) . '.';

    return '<svg xmlns="http://www.w3.org/2000/svg" class="pad-semaphore" role="img" aria-labelledby="' . "$id-title $id-desc" . '"'
         . ' width="' . padNotationNum ( $widest * $scale ) . '" height="' . padNotationNum ( $height * $scale ) . '" viewBox="0 0 ' . $widest . ' ' . $height . '">'
         . "<title id=\"$id-title\">" . padNotationAttr ( $title ) . '</title>'
         . "<desc id=\"$id-desc\">" . padNotationAttr ( $desc ) . '</desc>'
         . $body . '</svg>';

  }

?>
