<?php

  // Braille - the braille notation, {braille 'Hello'/}. A text written in Unified English
  // Braille, grade 1 (uncontracted: a cell per letter, no contractions), as the characters of
  // Unicode's braille block (U+2800 - U+28FF), whose eight bits are the eight dots: dot 1 is
  // bit 0, dot 6 bit 5.
  //
  // padBrailleCells   the cells of a text: [ [ dots, the characters they stand for ], ... ] and
  //                   the characters it has no cell for
  // padBrailleChar    the Unicode character of a cell from its dot numbers, '1245' is ⠛
  // padBrailleDots    the SVG of cells as raised dots, a line of cells per row
  //
  // The rules of UEB grade 1 written here: the letters a to z; a capital letter after the
  // capital sign ⠠ (dot 6), a word of two capitals or more after the capitals word indicator
  // ⠠⠠ - and the capitals terminator ⠠⠄ when small letters follow in the same word; a number
  // after the numeric indicator ⠼ (dots 3456) with the digits as the letters a to j, held
  // through a decimal point or comma between digits; after a number, a letter a to j takes
  // the grade 1 indicator ⠰ (dots 56) so it is not read as a digit; the punctuation and signs
  // of UEB; an accented letter as its accent's modifier before the letter (é is ⠘⠌⠑).
  // Contracted (grade 2) braille - the wordsigns and groupsigns - is not written: a text in
  // grade 1 says every letter, which is what a reader of a short label, a sign or a name needs.

  const PAD_BRAILLE_LETTERS = [
    'a' => '1',    'b' => '12',   'c' => '14',   'd' => '145',  'e' => '15',   'f' => '124',
    'g' => '1245', 'h' => '125',  'i' => '24',   'j' => '245',  'k' => '13',   'l' => '123',
    'm' => '134',  'n' => '1345', 'o' => '135',  'p' => '1234', 'q' => '12345','r' => '1235',
    's' => '234',  't' => '2345', 'u' => '136',  'v' => '1236', 'w' => '2456', 'x' => '1346',
    'y' => '13456','z' => '1356' ];

  // The digits are the first ten letters: 1 is a, 0 is j.

  const PAD_BRAILLE_DIGITS = [ '1' => 'a', '2' => 'b', '3' => 'c', '4' => 'd', '5' => 'e',
                               '6' => 'f', '7' => 'g', '8' => 'h', '9' => 'i', '0' => 'j' ];

  // Punctuation and signs of UEB, a cell or two each. The double quotation marks are the
  // non-specific ones: ⠦ opens, ⠴ closes - a straight " opens and closes by turns.

  const PAD_BRAILLE_SIGNS = [
    ','  => [ '2' ],          ';' => [ '23' ],          ':' => [ '25' ],          '.' => [ '256' ],
    '!'  => [ '235' ],        '?' => [ '236' ],         "'" => [ '3' ],           '’' => [ '3' ],
    '-'  => [ '36' ],         '–' => [ '6', '36' ],     '—' => [ '5', '6', '36' ],     '“' => [ '236' ],
    '”'  => [ '356' ],        '(' => [ '5', '126' ],    ')' => [ '5', '345' ],    '[' => [ '46', '126' ],
    ']'  => [ '46', '345' ],  '{' => [ '456', '126' ],  '}' => [ '456', '345' ],  '/' => [ '456', '34' ],
    '\\' => [ '456', '16' ],  '&' => [ '4', '12346' ],   '@' => [ '4', '1' ],      '*' => [ '5', '35' ],
    '#'  => [ '456', '1456' ],'%' => [ '46', '356' ],    '+' => [ '5', '235' ],    '=' => [ '5', '2356' ],
    '<'  => [ '4', '126' ],   '>' => [ '4', '345' ],    '$' => [ '4', '234' ],    '€' => [ '4', '15' ],
    '£'  => [ '4', '123' ],   '_' => [ '46', '36' ],    '~' => [ '4', '35' ],     '^' => [ '4', '26' ],
    '|'  => [ '456', '1256' ],'×' => [ '5', '236' ],    '÷' => [ '5', '34' ],     '°' => [ '45', '245' ] ];

  // The accented letters: the modifier of the accent, then the letter. Acute ⠘⠌, grave ⠘⠡,
  // circumflex ⠘⠩, diaeresis ⠘⠒, tilde ⠘⠻, cedilla ⠘⠯, ring ⠘⠫.

  const PAD_BRAILLE_ACCENTS = [
    'acute' => [ '45', '34' ],  'grave' => [ '45', '16' ],  'circumflex' => [ '45', '146' ],
    'diaeresis' => [ '45', '25' ], 'tilde' => [ '45', '12456' ], 'cedilla' => [ '45', '12346' ],
    'ring' => [ '45', '1246' ] ];

  const PAD_BRAILLE_ACCENTED = [
    'á' => [ 'a', 'acute' ], 'é' => [ 'e', 'acute' ], 'í' => [ 'i', 'acute' ], 'ó' => [ 'o', 'acute' ],
    'ú' => [ 'u', 'acute' ], 'ý' => [ 'y', 'acute' ], 'à' => [ 'a', 'grave' ], 'è' => [ 'e', 'grave' ],
    'ì' => [ 'i', 'grave' ], 'ò' => [ 'o', 'grave' ], 'ù' => [ 'u', 'grave' ], 'â' => [ 'a', 'circumflex' ],
    'ê' => [ 'e', 'circumflex' ], 'î' => [ 'i', 'circumflex' ], 'ô' => [ 'o', 'circumflex' ],
    'û' => [ 'u', 'circumflex' ], 'ä' => [ 'a', 'diaeresis' ], 'ë' => [ 'e', 'diaeresis' ],
    'ï' => [ 'i', 'diaeresis' ], 'ö' => [ 'o', 'diaeresis' ], 'ü' => [ 'u', 'diaeresis' ],
    'ÿ' => [ 'y', 'diaeresis' ], 'ã' => [ 'a', 'tilde' ], 'ñ' => [ 'n', 'tilde' ], 'õ' => [ 'o', 'tilde' ],
    'ç' => [ 'c', 'cedilla' ], 'å' => [ 'a', 'ring' ] ];

  // The colours of the drawn cells: a raised dot, an empty place, the character under a cell.

  const PAD_BRAILLE_COLOURS = [ 'dot' => [ '#1f2937', '#e5e7eb' ], 'empty' => [ '#b8bec6', '#59616b' ],
                                'text' => [ '#6b7280', '#9ca3af' ] ];

  function padBrailleChar ( $dots ) {

    $bits = 0;

    foreach ( str_split ( (string) $dots ) as $dot )
      if ( $dot >= '1' and $dot <= '8' )
        $bits |= 1 << ( (int) $dot - 1 );

    return mb_chr ( 0x2800 + $bits, 'UTF-8' );

  }

  // The cells of a text, each [ dots, what it stands for ] - the dots '' for a space between
  // words - and the characters there is no cell for. A cell that is a sign, an indicator or a
  // modifier stands for '' and is described by the cell after it.

  function padBrailleCells ( $text ) {

    $chars   = preg_split ( '//u', (string) $text, -1, PREG_SPLIT_NO_EMPTY );
    $count   = count ( $chars );
    $cells   = [];
    $unknown = [];
    $numeric = FALSE;
    $quote   = FALSE;
    $capital = FALSE;

    $isUpper = fn ( $c ) => $c !== mb_strtolower ( $c, 'UTF-8' ) and $c === mb_strtoupper ( $c, 'UTF-8' );
    $isAlpha = fn ( $c ) => isset ( PAD_BRAILLE_LETTERS [ mb_strtolower ( $c, 'UTF-8' ) ] ) or isset ( PAD_BRAILLE_ACCENTED [ mb_strtolower ( $c, 'UTF-8' ) ] );

    for ( $i = 0; $i < $count; $i++ ) {

      $char  = $chars [$i];
      $lower = mb_strtolower ( $char, 'UTF-8' );

      // A space ends a number and a word in capitals.

      if ( preg_match ( '/^\s$/u', $char ) ) {
        if ( ! $cells or end ( $cells ) [0] !== '' )
          $cells [] = [ '', ' ' ];
        $numeric = $capital = FALSE;
        continue;
      }

      if ( isset ( PAD_BRAILLE_DIGITS [$char] ) ) {
        if ( ! $numeric )
          $cells [] = [ '3456', '' ];
        $numeric  = TRUE;
        $cells [] = [ PAD_BRAILLE_LETTERS [ PAD_BRAILLE_DIGITS [$char] ], $char ];
        continue;
      }

      // A decimal point or a comma between two digits stays in the number.

      if ( $numeric and ( $char == '.' or $char == ',' ) and isset ( PAD_BRAILLE_DIGITS [ $chars [$i + 1] ?? '' ] ) ) {
        $cells [] = [ $char == '.' ? '256' : '2', $char ];
        continue;
      }

      if ( $isAlpha ( $char ) ) {

        // Capitals: a run of two or more takes the word indicator, held until the word ends
        // or a small letter comes - which then takes the terminator; one capital its sign.

        if ( $isUpper ( $char ) ) {
          if ( ! $capital ) {
            $run = 0;
            for ( $j = $i; $j < $count and $isAlpha ( $chars [$j] ) and $isUpper ( $chars [$j] ); $j++ )
              $run++;
            if ( $run >= 2 ) {
              $cells [] = [ '6', '' ];
              $cells [] = [ '6', '' ];
              $capital  = TRUE;
            } else
              $cells [] = [ '6', '' ];
          }
        } elseif ( $capital ) {
          $cells [] = [ '6', '' ];
          $cells [] = [ '3', '' ];
          $capital  = FALSE;
        }

        $base = isset ( PAD_BRAILLE_ACCENTED [$lower] ) ? PAD_BRAILLE_ACCENTED [$lower] [0] : $lower;

        // A letter a to j right after a number would be read as a digit.

        if ( $numeric and strpos ( 'abcdefghij', $base ) !== FALSE and ! $isUpper ( $char )
             and ! isset ( PAD_BRAILLE_ACCENTED [$lower] ) )
          $cells [] = [ '56', '' ];

        $numeric = FALSE;

        if ( isset ( PAD_BRAILLE_ACCENTED [$lower] ) )
          foreach ( PAD_BRAILLE_ACCENTS [ PAD_BRAILLE_ACCENTED [$lower] [1] ] as $dots )
            $cells [] = [ $dots, '' ];

        $cells [] = [ PAD_BRAILLE_LETTERS [$base], $char ];
        continue;

      }

      // Any other character ends a number, and a word in capitals: the indicator reaches over
      // a run of letters.

      $numeric = $capital = FALSE;

      if ( $char == '"' ) {
        $cells [] = [ $quote ? '356' : '236', $char ];
        $quote    = ! $quote;
        continue;
      }

      if ( isset ( PAD_BRAILLE_SIGNS [$char] ) ) {
        $signs = PAD_BRAILLE_SIGNS [$char];
        foreach ( $signs as $at => $dots )
          $cells [] = [ $dots, ( $at == count ( $signs ) - 1 ) ? $char : '' ];
        continue;
      }

      $unknown [$char] = TRUE;

    }

    while ( $cells and end ( $cells ) [0] === '' )
      array_pop ( $cells );

    return [ $cells, array_keys ( $unknown ) ];

  }

  // The cells as dots: a cell is two columns of three dots, a raised dot drawn full and the
  // other places as small rings so the pattern reads; a line holds at most $across cells and
  // breaks at a space. Under each cell, small, the character it stands for.

  function padBrailleDots ( $cells, $across, $id, $label ) {

    $lines = [ [] ];

    // Words are kept whole on a line where they fit.

    $words = [ [] ];

    foreach ( $cells as $cell )
      if ( $cell [0] === '' )
        $words [] = [];
      else
        $words [ count ( $words ) - 1 ] [] = $cell;

    foreach ( $words as $word ) {
      $line = count ( $lines ) - 1;
      $need = count ( $word ) + ( $lines [$line] ? 1 : 0 );
      if ( $lines [$line] and count ( $lines [$line] ) + $need > $across )
        $lines [ ++$line ] = [];
      if ( $lines [$line] )
        $lines [$line] [] = [ '', ' ' ];
      foreach ( $word as $cell )
        $lines [$line] [] = $cell;
    }

    $cellW = 22;
    $lineH = 46;
    $width = max ( 1, max ( array_map ( 'count', $lines ) ) ) * $cellW + 6;
    $height = count ( $lines ) * $lineH + 4;

    $svg = '<svg xmlns="http://www.w3.org/2000/svg" class="pad-braille-cells" role="img" aria-labelledby="' . $id . '-title"'
         . ' width="' . padNotationNum ( $width * 1.6 ) . '" height="' . padNotationNum ( $height * 1.6 ) . '" viewBox="0 0 ' . $width . ' ' . $height . '">'
         . '<title id="' . $id . '-title">' . padNotationAttr ( $label ) . '</title>';

    foreach ( $lines as $row => $line )
      foreach ( $line as $col => list ( $dots, $char ) ) {

        if ( $dots === '' )
          continue;

        $x = 6 + $col * $cellW;
        $y = 4 + $row * $lineH;

        foreach ( [ 1 => [ 0, 0 ], 2 => [ 0, 1 ], 3 => [ 0, 2 ], 4 => [ 1, 0 ], 5 => [ 1, 1 ], 6 => [ 1, 2 ] ] as $dot => list ( $c, $r ) ) {
          $cx = $x + 3 + $c * 9;
          $cy = $y + 3 + $r * 9;
          $svg .= str_contains ( $dots, (string) $dot )
                ? "<circle class=\"pbr-on\" cx=\"$cx\" cy=\"$cy\" r=\"3\"/>"
                : "<circle class=\"pbr-off\" cx=\"$cx\" cy=\"$cy\" r=\"1.2\"/>";
        }

        if ( $char !== '' and $char !== ' ' )
          $svg .= '<text class="pbr-char" x="' . ( $x + 7.5 ) . '" y="' . ( $y + 36 ) . '" text-anchor="middle">' . padNotationAttr ( $char ) . '</text>';

      }

    return "$svg</svg>";

  }

?>
