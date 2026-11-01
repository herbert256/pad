<?php

  // Signals - the signals notation, {signals 'PAD'/}. A text spelt in the flags of the
  // International Code of Signals: a flag per letter, a numeral pennant per digit, drawn in
  // the colours and patterns of the code - red, blue, yellow, black and white - a flag 60 by 50,
  // a pennant 80 by 40 tapering to its fly.
  //
  // padSignalsFlag   the SVG of one flag or pennant, its hoist at x 0
  // padSignalsSvg    the text as a row of flags, a word apart from the next, each flag with its
  //                  letter under it and, on hover, its name and its meaning as a single flag
  //
  // Every flag is a nested <svg>, which clips what it draws to its own box - the saltires of M
  // and V and the stripes of Y run off the edge and are cut there - and the pennants share a
  // clip path of their shape, with an id of the drawing's own.

  // The colours of the code: they are the flags' and stay the same in a dark page; the outline
  // keeps a white flag seen against a white page.

  const PAD_SIGNALS_COLOURS = [ 'outline' => [ '#4b5563', '#9ca3af' ], 'text' => [ '#4b5563', '#9ca3af' ] ];

  const PAD_SIGNALS_RED    = '#d0202e';
  const PAD_SIGNALS_BLUE   = '#1c3f94';
  const PAD_SIGNALS_YELLOW = '#f6c400';
  const PAD_SIGNALS_BLACK  = '#151515';
  const PAD_SIGNALS_WHITE  = '#ffffff';

  // The name of each flag in the phonetic alphabet, and what it says hoisted alone.

  const PAD_SIGNALS_NAMES = [
    'A' => [ 'Alfa',     'I have a diver down; keep well clear at slow speed' ],
    'B' => [ 'Bravo',    'I am taking in, discharging or carrying dangerous goods' ],
    'C' => [ 'Charlie',  'Affirmative' ],
    'D' => [ 'Delta',    'Keep clear of me; I am manoeuvring with difficulty' ],
    'E' => [ 'Echo',     'I am altering my course to starboard' ],
    'F' => [ 'Foxtrot',  'I am disabled; communicate with me' ],
    'G' => [ 'Golf',     'I require a pilot' ],
    'H' => [ 'Hotel',    'I have a pilot on board' ],
    'I' => [ 'India',    'I am altering my course to port' ],
    'J' => [ 'Juliett',  'I am on fire and have dangerous cargo on board: keep well clear of me' ],
    'K' => [ 'Kilo',     'I wish to communicate with you' ],
    'L' => [ 'Lima',     'You should stop your vessel instantly' ],
    'M' => [ 'Mike',     'My vessel is stopped and making no way through the water' ],
    'N' => [ 'November', 'Negative' ],
    'O' => [ 'Oscar',    'Man overboard' ],
    'P' => [ 'Papa',     'In harbour: all persons should report on board, the vessel is about to put to sea' ],
    'Q' => [ 'Quebec',   'My vessel is healthy and I request free pratique' ],
    'R' => [ 'Romeo',    'No meaning as a single flag' ],
    'S' => [ 'Sierra',   'I am operating astern propulsion' ],
    'T' => [ 'Tango',    'Keep clear of me; I am engaged in pair trawling' ],
    'U' => [ 'Uniform',  'You are running into danger' ],
    'V' => [ 'Victor',   'I require assistance' ],
    'W' => [ 'Whiskey',  'I require medical assistance' ],
    'X' => [ 'X-ray',    'Stop carrying out your intentions and watch for my signals' ],
    'Y' => [ 'Yankee',   'I am dragging my anchor' ],
    'Z' => [ 'Zulu',     'I require a tug' ] ];

  const PAD_SIGNALS_DIGITS = [ '0' => 'zero', '1' => 'one', '2' => 'two', '3' => 'three', '4' => 'four',
                               '5' => 'five', '6' => 'six', '7' => 'seven', '8' => 'eight', '9' => 'nine' ];

  // A rectangle and a polygon of the flags, in a colour.

  function padSignalsRect ( $x, $y, $w, $h, $fill ) {

    return "<rect x=\"$x\" y=\"$y\" width=\"$w\" height=\"$h\" fill=\"$fill\"/>";

  }

  function padSignalsPoly ( $points, $fill ) {

    return "<polygon points=\"$points\" fill=\"$fill\"/>";

  }

  // The drawing of a letter's flag on a box of 60 by 50, the hoist at the left: its colours
  // and pattern as the code has them.

  function padSignalsLetter ( $letter ) {

    list ( $r, $b, $y, $k, $w ) = [ PAD_SIGNALS_RED, PAD_SIGNALS_BLUE, PAD_SIGNALS_YELLOW, PAD_SIGNALS_BLACK, PAD_SIGNALS_WHITE ];

    $rect  = fn ( $x, $y0, $wd, $ht, $f ) => padSignalsRect ( $x, $y0, $wd, $ht, $f );
    $poly  = fn ( $p, $f ) => padSignalsPoly ( $p, $f );
    $field = fn ( $f ) => $rect ( 0, 0, 60, 50, $f );
    $cross = fn ( $f ) => $rect ( 25, 0, 10, 50, $f ) . $rect ( 0, 20, 60, 10, $f );
    $saltire = fn ( $f ) => "<path d=\"M0 0L60 50M60 0L0 50\" stroke=\"$f\" stroke-width=\"10\"/>";

    switch ( $letter ) {

      case 'A': return $rect ( 0, 0, 30, 50, $w ) . $poly ( '30,0 60,0 45,25 60,50 30,50', $b );
      case 'B': return $poly ( '0,0 60,0 45,25 60,50 0,50', $r );
      case 'C': return $rect ( 0, 0, 60, 10, $b ) . $rect ( 0, 10, 60, 10, $w ) . $rect ( 0, 20, 60, 10, $r )
                     . $rect ( 0, 30, 60, 10, $w ) . $rect ( 0, 40, 60, 10, $b );
      case 'D': return $field ( $y ) . $rect ( 0, 12.5, 60, 25, $b );
      case 'E': return $rect ( 0, 0, 60, 25, $b ) . $rect ( 0, 25, 60, 25, $r );
      case 'F': return $field ( $w ) . $poly ( '30,0 60,25 30,50 0,25', $r );
      case 'G': return $field ( $b ) . $rect ( 0, 0, 10, 50, $y ) . $rect ( 20, 0, 10, 50, $y ) . $rect ( 40, 0, 10, 50, $y );
      case 'H': return $rect ( 0, 0, 30, 50, $w ) . $rect ( 30, 0, 30, 50, $r );
      case 'I': return $field ( $y ) . "<circle cx=\"30\" cy=\"25\" r=\"12.5\" fill=\"$k\"/>";
      case 'J': return $field ( $b ) . $rect ( 0, 16.67, 60, 16.67, $w );
      case 'K': return $rect ( 0, 0, 30, 50, $y ) . $rect ( 30, 0, 30, 50, $b );
      case 'L': return $field ( $y ) . $rect ( 30, 0, 30, 25, $k ) . $rect ( 0, 25, 30, 25, $k );
      case 'M': return $field ( $b ) . $saltire ( $w );
      case 'N':
        $checks = $field ( $w );
        for ( $row = 0; $row < 4; $row++ )
          for ( $col = 0; $col < 4; $col++ )
            if ( ( $row + $col ) % 2 == 0 )
              $checks .= $rect ( $col * 15, $row * 12.5, 15, 12.5, $b );
        return $checks;
      case 'O': return $poly ( '0,0 0,50 60,50', $y ) . $poly ( '0,0 60,0 60,50', $r );
      case 'P': return $field ( $b ) . $rect ( 20, 16.67, 20, 16.67, $w );
      case 'Q': return $field ( $y );
      case 'R': return $field ( $r ) . $cross ( $y );
      case 'S': return $field ( $w ) . $rect ( 20, 16.67, 20, 16.67, $b );
      case 'T': return $rect ( 0, 0, 20, 50, $r ) . $rect ( 20, 0, 20, 50, $w ) . $rect ( 40, 0, 20, 50, $b );
      case 'U': return $field ( $w ) . $rect ( 0, 0, 30, 25, $r ) . $rect ( 30, 25, 30, 25, $r );
      case 'V': return $field ( $w ) . $saltire ( $r );
      case 'W': return $field ( $b ) . $rect ( 10, 8.33, 40, 33.33, $w ) . $rect ( 20, 16.67, 20, 16.67, $r );
      case 'X': return $field ( $w ) . $cross ( $b );
      // Yankee: yellow and red stripes rising from the lower hoist to the upper fly, a yellow
      // one in the upper corner of the hoist - each from x + 50 at the top to x at the bottom.

      case 'Y':
        $stripes = $field ( $r );
        for ( $i = -6; $i <= 6; $i += 2 )
          $stripes .= $poly ( ( $i * 8.5 + 50 ) . ',0 ' . ( $i * 8.5 + 58.5 ) . ',0 ' . ( $i * 8.5 + 8.5 ) . ',50 ' . ( $i * 8.5 ) . ',50', $y );
        return $stripes;
      case 'Z': return $poly ( '0,0 60,0 30,25', $y ) . $poly ( '60,0 60,50 30,25', $b )
                     . $poly ( '0,50 60,50 30,25', $r ) . $poly ( '0,0 0,50 30,25', $k );

    }

    return '';

  }

  // The drawing of a digit's numeral pennant on a box of 80 by 40, clipped to the pennant's
  // shape by the caller.

  function padSignalsPennant ( $digit ) {

    list ( $r, $b, $y, $k, $w ) = [ PAD_SIGNALS_RED, PAD_SIGNALS_BLUE, PAD_SIGNALS_YELLOW, PAD_SIGNALS_BLACK, PAD_SIGNALS_WHITE ];

    $rect  = fn ( $x, $y0, $wd, $ht, $f ) => padSignalsRect ( $x, $y0, $wd, $ht, $f );
    $field = fn ( $f ) => $rect ( 0, 0, 80, 40, $f );
    $cross = fn ( $f ) => $rect ( 16, 0, 9, 40, $f ) . $rect ( 0, 15.5, 80, 9, $f );

    switch ( $digit ) {

      case '1': return $field ( $w ) . "<circle cx=\"20.5\" cy=\"20\" r=\"9\" fill=\"$r\"/>";
      case '2': return $field ( $b ) . "<circle cx=\"20.5\" cy=\"20\" r=\"9\" fill=\"$w\"/>";
      case '3': return $rect ( 0, 0, 27, 40, $r ) . $rect ( 27, 0, 26, 40, $w ) . $rect ( 53, 0, 27, 40, $b );
      case '4': return $field ( $r ) . $cross ( $w );
      case '5': return $rect ( 0, 0, 40, 40, $y ) . $rect ( 40, 0, 40, 40, $b );
      case '6': return $rect ( 0, 0, 80, 20, $k ) . $rect ( 0, 20, 80, 20, $w );
      case '7': return $rect ( 0, 0, 80, 20, $y ) . $rect ( 0, 20, 80, 20, $r );
      case '8': return $field ( $w ) . $cross ( $r );
      case '9': return $rect ( 0, 0, 40, 20, $w ) . $rect ( 40, 0, 40, 20, $k ) . $rect ( 0, 20, 40, 20, $r ) . $rect ( 40, 20, 40, 20, $y );
      case '0': return $rect ( 0, 0, 27, 40, $y ) . $rect ( 27, 0, 26, 40, $r ) . $rect ( 53, 0, 27, 40, $y );

    }

    return '';

  }

  // The signs of a text: its letters and digits upper-cased, words apart; and the characters
  // the code has no flag for.

  function padSignalsWords ( $text ) {

    $words   = [];
    $unknown = [];

    foreach ( preg_split ( '/\s+/u', trim ( (string) $text ) ) as $word ) {
      $signs = [];
      foreach ( preg_split ( '//u', $word, -1, PREG_SPLIT_NO_EMPTY ) as $char ) {
        $upper = strtoupper ( $char );
        if ( isset ( PAD_SIGNALS_NAMES [$upper] ) or isset ( PAD_SIGNALS_DIGITS [$char] ) )
          $signs [] = $upper;
        else
          $unknown [$char] = TRUE;
      }
      if ( $signs )
        $words [] = $signs;
    }

    return [ $words, array_keys ( $unknown ) ];

  }

  // What a flag is called: "H - Hotel: I have a pilot on board", "pennant 4".

  function padSignalsName ( $sign, $meaning = TRUE ) {

    if ( isset ( PAD_SIGNALS_DIGITS [$sign] ) )
      return "$sign - numeral pennant " . PAD_SIGNALS_DIGITS [$sign];

    return "$sign - " . PAD_SIGNALS_NAMES [$sign] [0] . ( $meaning ? ': ' . PAD_SIGNALS_NAMES [$sign] [1] : '' );

  }

  // The text as rows of flags: a flag takes 70 across, a pennant 90, a word 30 more; a row
  // holds at most $across units and breaks between words.

  function padSignalsSvg ( $words, $size, $id, $title ) {

    $rows = [ [] ];
    $used = 0;
    $wide = fn ( $sign ) => isset ( PAD_SIGNALS_DIGITS [$sign] ) ? 90 : 70;

    foreach ( $words as $word ) {
      $length = array_sum ( array_map ( $wide, $word ) );
      if ( $rows [ count ( $rows ) - 1 ] and $used + 30 + $length > 720 ) {
        $rows [] = [];
        $used    = 0;
      }
      $used += ( $rows [ count ( $rows ) - 1 ] ? 30 : 0 ) + $length;
      $rows [ count ( $rows ) - 1 ] [] = $word;
    }

    $rowH   = 80;
    $body   = '';
    $widest = 0;

    foreach ( $rows as $row => $line ) {

      $x = 0;

      foreach ( $line as $index => $word ) {

        if ( $index )
          $x += 30;

        foreach ( $word as $sign ) {

          $top    = 6 + $row * $rowH;
          $pennant = isset ( PAD_SIGNALS_DIGITS [$sign] );
          $body  .= '<g class="psg-flag"><title>' . padNotationAttr ( padSignalsName ( $sign ) ) . '</title>';

          if ( $pennant )
            $body .= '<svg x="' . ( $x + 5 ) . '" y="' . ( $top + 5 ) . '" width="80" height="40" viewBox="0 0 80 40">'
                   . "<g clip-path=\"url(#$id-pennant)\">" . padSignalsPennant ( $sign ) . '</g>'
                   . '<polygon class="psg-edge" points="0.5,0.5 79.5,15 79.5,25 0.5,39.5"/></svg>';
          else
            $body .= '<svg x="' . ( $x + 5 ) . '" y="' . $top . '" width="60" height="50" viewBox="0 0 60 50">'
                   . padSignalsLetter ( $sign )
                   . ( ( $sign == 'A' or $sign == 'B' ) ? '<polygon class="psg-edge" points="0.5,0.5 59.5,0.5 45,25 59.5,49.5 0.5,49.5"/>'
                                                        : '<rect class="psg-edge" x="0.5" y="0.5" width="59" height="49"/>' )
                   . '</svg>';

          $body .= '<text class="psg-char" x="' . ( $x + 5 + ( $pennant ? 40 : 30 ) ) . '" y="' . ( $top + 68 ) . '" text-anchor="middle">'
                 . $sign . '</text></g>';

          $x += $wide ( $sign );

        }

      }

      $widest = max ( $widest, $x );

    }

    $width  = $widest;
    $height = count ( $rows ) * $rowH;
    $scale  = $size / 60;

    $desc = implode ( '; ', array_map ( fn ( $word ) => implode ( ', ', array_map ( fn ( $sign ) => padSignalsName ( $sign, FALSE ), $word ) ), $words ) ) . '.';

    return '<svg xmlns="http://www.w3.org/2000/svg" class="pad-signals" role="img" aria-labelledby="' . "$id-title $id-desc" . '"'
         . ' width="' . padNotationNum ( $width * $scale ) . '" height="' . padNotationNum ( $height * $scale ) . '" viewBox="0 0 ' . $width . ' ' . $height . '">'
         . "<title id=\"$id-title\">" . padNotationAttr ( $title ) . '</title>'
         . "<desc id=\"$id-desc\">" . padNotationAttr ( $desc ) . '</desc>'
         . "<defs><clipPath id=\"$id-pennant\"><polygon points=\"0,0 80,15 80,25 0,40\"/></clipPath></defs>"
         . $body . '</svg>';

  }

?>
