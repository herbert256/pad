<?php

  // Morse - the morse notation, {morse 'SOS'/}. A text in the international Morse code of
  // ITU-R M.1677-1: its letters, its digits and its punctuation, and the accented e it has -
  // nothing else, so a character it has no sign for (! & ;) is named, not guessed.
  //
  // padMorseCodes   the signs of a text: a list of words, each a list of [ character, code ],
  //                 the code written with . and -, and the characters it has no sign for
  // padMorseSvg     the signal as it sounds, drawn: a dot one unit, a dash three, the gap in a
  //                 character one unit, between characters three, between words seven - the
  //                 timing of the recommendation - a line of words per row

  const PAD_MORSE_CODES = [
    'a' => '.-',     'b' => '-...',   'c' => '-.-.',   'd' => '-..',    'e' => '.',      'f' => '..-.',
    'g' => '--.',    'h' => '....',   'i' => '..',     'j' => '.---',   'k' => '-.-',    'l' => '.-..',
    'm' => '--',     'n' => '-.',     'o' => '---',    'p' => '.--.',   'q' => '--.-',   'r' => '.-.',
    's' => '...',    't' => '-',      'u' => '..-',    'v' => '...-',   'w' => '.--',    'x' => '-..-',
    'y' => '-.--',   'z' => '--..',   'é' => '..-..',
    '1' => '.----',  '2' => '..---',  '3' => '...--',  '4' => '....-',  '5' => '.....',  '6' => '-....',
    '7' => '--...',  '8' => '---..',  '9' => '----.',  '0' => '-----',
    '.' => '.-.-.-', ',' => '--..--', ':' => '---...', '?' => '..--..', "'" => '.----.', '’' => '.----.',
    '-' => '-....-', '/' => '-..-.',  '(' => '-.--.',  ')' => '-.--.-', '"' => '.-..-.', '“' => '.-..-.',
    '”' => '.-..-.', '=' => '-...-',  '+' => '.-.-.',  '×' => '-..-',   '@' => '.--.-.' ];

  // The colours of the drawn signal: the marks, the line they stand on.

  const PAD_MORSE_COLOURS = [ 'mark' => [ '#1f2937', '#e5e7eb' ], 'rule' => [ '#d6dae0', '#3f4650' ],
                              'text' => [ '#6b7280', '#9ca3af' ] ];

  function padMorseCodes ( $text ) {

    $words   = [];
    $unknown = [];

    foreach ( preg_split ( '/\s+/u', trim ( (string) $text ) ) as $word ) {

      $signs = [];

      foreach ( preg_split ( '//u', $word, -1, PREG_SPLIT_NO_EMPTY ) as $char ) {
        $lower = mb_strtolower ( $char, 'UTF-8' );
        if ( isset ( PAD_MORSE_CODES [$lower] ) )
          $signs [] = [ $char, PAD_MORSE_CODES [$lower] ];
        else
          $unknown [$char] = TRUE;
      }

      if ( $signs )
        $words [] = $signs;

    }

    return [ $words, array_keys ( $unknown ) ];

  }

  // The code as text: the signs of a word apart by a space, the words by a slash - the way
  // morse is written down - with the dot and the dash as · and −, which read better than the
  // full stop and the hyphen.

  function padMorseText ( $words ) {

    return implode ( ' / ', array_map ( fn ( $word ) => implode ( ' ', array_map ( fn ( $sign ) => strtr ( $sign [1], [ '.' => '·', '-' => '−' ] ), $word ) ), $words ) );

  }

  // The code in words, for a screen reader: "S: dot dot dot. O: dash dash dash."

  function padMorseSpoken ( $words ) {

    $said = [];

    foreach ( $words as $word )
      foreach ( $word as list ( $char, $code ) )
        $said [] = mb_strtoupper ( $char, 'UTF-8' ) . ': ' . implode ( ' ', array_map ( fn ( $one ) => $one == '.' ? 'dot' : 'dash', str_split ( $code ) ) );

    return implode ( '. ', $said ) . '.';

  }

  // The signal drawn on a time line: a unit is 6 wide, a dot a round mark of one unit, a dash
  // a bar of three; a row holds at most $units units and breaks between words, each character
  // with its letter under it.

  function padMorseSvg ( $words, $units ) {

    $unit = 6;
    $rows = [ [] ];
    $used = 0;

    foreach ( $words as $word ) {

      $length = 0;

      foreach ( $word as $at => list ( , $code ) )
        $length += ( $at ? 3 : 0 ) + strlen ( $code ) * 2 - 1 + substr_count ( $code, '-' ) * 2;

      if ( $rows [ count ( $rows ) - 1 ] and $used + 7 + $length > $units ) {
        $rows [] = [];
        $used    = 0;
      }

      $used += ( $rows [ count ( $rows ) - 1 ] ? 7 : 0 ) + $length;
      $rows [ count ( $rows ) - 1 ] [] = $word;

    }

    $rowH   = 38;
    $marks  = '';
    $letters = '';
    $widest = 0;

    foreach ( $rows as $row => $line ) {

      $x = 0;
      $y = 6 + $row * $rowH;

      foreach ( $line as $index => $word ) {

        if ( $index )
          $x += 7;

        foreach ( $word as $at => list ( $char, $code ) ) {

          if ( $at )
            $x += 3;

          $start = $x;

          foreach ( str_split ( $code ) as $place => $signal ) {
            if ( $place )
              $x += 1;
            $long   = ( $signal == '-' ) ? 3 : 1;
            $marks .= '<rect class="pmo-mark" x="' . ( 4 + $x * $unit ) . '" y="' . $y . '" width="' . ( $long * $unit ) . '" height="' . $unit . '" rx="3"/>';
            $x     += $long;
          }

          $letters .= '<text class="pmo-char" x="' . padNotationNum ( 4 + ( $start + $x ) * $unit / 2 ) . '" y="' . ( $y + 22 ) . '" text-anchor="middle">'
                    . padNotationAttr ( mb_strtoupper ( $char, 'UTF-8' ) ) . '</text>';

        }

      }

      $widest = max ( $widest, $x );

    }

    $width  = 8 + $widest * $unit;
    $height = count ( $rows ) * $rowH;

    return '<svg xmlns="http://www.w3.org/2000/svg" class="pad-morse-signal" aria-hidden="true" focusable="false"'
         . ' width="' . $width . '" height="' . $height . '" viewBox="0 0 ' . $width . ' ' . $height . '">'
         . $marks . $letters . '</svg>';

  }

?>
