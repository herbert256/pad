<?php

  // Crosswords - the {crossword} tag. Words and their clues go in, a grid comes out: the
  // words laid across and down so they cross, the squares numbered the way a newspaper
  // numbers them, the empty grid as inline SVG and the clues as the Across and Down lists.
  //
  //   {crossword}
  //     ECHO: Writes a value without the sanitize chain
  //     PIPE: The | that hands a value to a function
  //   {/crossword}
  //
  // padCrosswordWords   the words and clues of the content: [ word, letters, clue ] rows,
  //                     or the reason a line is wrong as a string
  // padCrosswordLay     the words laid out - placed [ word, row, col, across, clue, length ]
  //                     rows - and the words that found no place
  // padCrossword        the grid and the clue lists
  //
  // The layout is greedy and the same every time: the longest word first, across; then
  // each next word - longest first, the order written between equals - where it crosses
  // the most letters already down, on the smallest grid, never side by side with another
  // word. A word that crosses nothing is tried again after the others, and left out when
  // it still fits nowhere: the clue lists then name it below the grid.

  function padCrosswordWords ( $text ) {

    $words = [];
    $seen  = [];

    foreach ( preg_split ( '/\R/', (string) $text ) as $line ) {

      $line = trim ( $line );

      if ( $line === '' )
        continue;

      if ( strpos ( $line, ':' ) === FALSE )
        return "the line '$line' has no ':' - write WORD: clue";

      list ( $word, $clue ) = array_map ( 'trim', explode ( ':', $line, 2 ) );

      $word    = mb_strtoupper ( preg_replace ( '/[\s\'-]+/u', '', $word ), 'UTF-8' );
      $letters = mb_str_split ( $word, 1, 'UTF-8' );

      if ( $word === '' or ! preg_match ( '/^\p{L}+$/u', $word ) )
        return "the word of '$line' is no word of letters";

      if ( $clue === '' )
        return "the word $word has no clue";

      if ( isset ( $seen [$word] ) )
        return "the word $word is there twice";

      $seen [$word] = TRUE;
      $words []     = [ $word, $letters, $clue ];

    }

    return $words;

  }

  function padCrosswordLay ( $words ) {

    $order = array_keys ( $words );

    usort ( $order, fn ( $a, $b ) => [ count ( $words [$b] [1] ), $a ] <=> [ count ( $words [$a] [1] ), $b ] );

    $grid   = [];
    $placed = [];
    $left   = $order;

    // Rounds until one places nothing: a word that crossed nothing yet may cross a word
    // placed after it.

    do {

      $more = FALSE;
      $wait = [];

      foreach ( $left as $index ) {

        $letters = $words [$index] [1];
        $spot    = $placed ? padCrosswordSpot ( $grid, $letters ) : [ 0, 0, TRUE ];

        if ( $spot === NULL ) {
          $wait [] = $index;
          continue;
        }

        list ( $row, $col, $across ) = $spot;

        foreach ( $letters as $i => $letter ) {
          $key = $across ? $row . ',' . ( $col + $i ) : ( $row + $i ) . ',' . $col;
          $grid [$key] = [ $letter, ( $grid [$key] [1] ?? 0 ) | ( $across ? 1 : 2 ) ];
        }

        $placed [$index] = [ $words [$index] [0], $row, $col, $across, $words [$index] [2], count ( $letters ) ];
        $more            = TRUE;

      }

      $left = $wait;

    } while ( $more and $left );

    ksort ( $placed );

    $skipped = [];

    foreach ( $left as $index )
      $skipped [] = $words [$index] [0];

    return [ $placed, $skipped ];

  }

  // The best place for a word: through every letter already down that it shares, in the
  // other direction from the word that letter belongs to. The most crossings win, then the
  // smallest grid, then the first found.

  function padCrosswordSpot ( $grid, $letters ) {

    $bounds = padCrosswordBounds ( array_keys ( $grid ) );
    $best   = NULL;
    $score  = NULL;

    foreach ( $grid as $key => list ( $letter, $dirs ) ) {

      if ( $dirs == 3 )
        continue;

      list ( $r, $c ) = array_map ( 'intval', explode ( ',', $key ) );
      $across         = ( $dirs == 2 );

      foreach ( $letters as $i => $mine ) {

        if ( $mine !== $letter )
          continue;

        $row = $across ? $r : $r - $i;
        $col = $across ? $c - $i : $c;
        $hit = padCrosswordFits ( $grid, $letters, $row, $col, $across );

        if ( ! $hit )
          continue;

        $end  = $across ? [ $row, $col + count ( $letters ) - 1 ] : [ $row + count ( $letters ) - 1, $col ];
        $area = ( max ( $bounds [2], $end [0] ) - min ( $bounds [0], $row ) + 1 ) * ( max ( $bounds [3], $end [1] ) - min ( $bounds [1], $col ) + 1 );
        $try  = [ $hit, -$area ];

        if ( $score === NULL or $try > $score ) {
          $score = $try;
          $best  = [ $row, $col, $across ];
        }

      }

    }

    return $best;

  }

  // The crossings of a word at a place, or 0 when it does not fit there: a square taken
  // holds the same letter in the other direction, a new square has no neighbour beside
  // it, and the squares before the first letter and after the last are empty.

  function padCrosswordFits ( $grid, $letters, $row, $col, $across ) {

    $dr = $across ? 0 : 1;
    $dc = $across ? 1 : 0;
    $n  = count ( $letters );

    if ( isset ( $grid [ ( $row - $dr ) . ',' . ( $col - $dc ) ] ) or isset ( $grid [ ( $row + $dr * $n ) . ',' . ( $col + $dc * $n ) ] ) )
      return 0;

    $hits = 0;

    foreach ( $letters as $i => $letter ) {

      $r   = $row + $dr * $i;
      $c   = $col + $dc * $i;
      $key = "$r,$c";

      if ( isset ( $grid [$key] ) ) {
        if ( $grid [$key] [0] !== $letter or $grid [$key] [1] & ( $across ? 1 : 2 ) )
          return 0;
        $hits++;
      } elseif ( isset ( $grid [ ( $r + $dc ) . ',' . ( $c + $dr ) ] ) or isset ( $grid [ ( $r - $dc ) . ',' . ( $c - $dr ) ] ) )
        return 0;

    }

    return $hits;

  }

  // [ top, left, bottom, right ] of a set of 'row,col' keys.

  function padCrosswordBounds ( $keys ) {

    $rows = $cols = [];

    foreach ( $keys as $key ) {
      list ( $r, $c ) = explode ( ',', $key );
      $rows [] = (int) $r;
      $cols [] = (int) $c;
    }

    return [ min ( $rows ), min ( $cols ), max ( $rows ), max ( $cols ) ];

  }

  // The grid and the clues. The squares are numbered row by row where a word starts; the
  // SVG draws the letter squares only, each with its number and - with $solution - its
  // letter. The clue lists are ordered lists whose items carry the number as their value.

  function padCrossword ( $placed, $skipped, $solution, $title ) {

    $h    = fn ( $t ) => htmlspecialchars ( (string) $t, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );
    $grid = [];

    foreach ( $placed as list ( $word, $row, $col, $across ) )
      foreach ( mb_str_split ( $word, 1, 'UTF-8' ) as $i => $letter )
        $grid [ $across ? $row . ',' . ( $col + $i ) : ( $row + $i ) . ',' . $col ] = $letter;

    list ( $top, $left, $bottom, $right ) = padCrosswordBounds ( array_keys ( $grid ) );

    // The numbers: row by row, a number for every square a word starts on.

    $starts = [];

    foreach ( $placed as $index => list ( $word, $row, $col ) )
      $starts [ ( $row - $top ) * 10000 + ( $col - $left ) ] [] = $index;

    ksort ( $starts );

    $number = $numbers = $clues = [];
    $next   = 1;

    foreach ( $starts as $at => $indexes ) {
      $number [ intdiv ( $at, 10000 ) . ',' . ( $at % 10000 ) ] = $next;
      foreach ( $indexes as $index ) {
        $numbers [$index] = $next;
        $clues [ $placed [$index] [3] ? 'Across' : 'Down' ] [] = $index;
      }
      $next++;
    }

    // The SVG: 36 units a square, a unit of margin for the outer lines.

    $size   = 36;
    $width  = ( $right - $left + 1 ) * $size + 2;
    $height = ( $bottom - $top + 1 ) * $size + 2;
    $id     = padCrosswordId ( [ $placed, $solution ] );
    $title  = ( $title !== '' ) ? $title : 'Crossword';
    $desc   = count ( $placed ) . ' words on a grid of ' . ( $right - $left + 1 ) . ' by ' . ( $bottom - $top + 1 ) . ' squares';

    if ( $solution ) {
      $filled = [];
      foreach ( [ 'Across', 'Down' ] as $way )
        foreach ( $clues [$way] ?? [] as $index )
          $filled [] = $numbers [$index] . ' ' . strtolower ( $way ) . ' ' . $placed [$index] [0];
      $desc .= ', filled in: ' . implode ( ', ', $filled );
    }

    if ( $skipped )
      $desc .= '; not placed: ' . implode ( ', ', $skipped );

    $svg = '<svg xmlns="http://www.w3.org/2000/svg" class="pcw-grid" role="img" aria-labelledby="' . "$id-title $id-desc\""
         . " width=\"$width\" height=\"$height\" viewBox=\"0 0 $width $height\">"
         . "<title id=\"$id-title\">" . $h ( $title ) . '</title>'
         . "<desc id=\"$id-desc\">" . $h ( $desc ) . '</desc>';

    ksort ( $grid );

    foreach ( $grid as $key => $letter ) {

      list ( $r, $c ) = explode ( ',', $key );
      $r -= $top;
      $c -= $left;
      $x  = 1 + $c * $size;
      $y  = 1 + $r * $size;

      $svg .= "<rect class=\"pcw-square\" x=\"$x\" y=\"$y\" width=\"$size\" height=\"$size\"/>";

      if ( isset ( $number [ "$r,$c" ] ) )
        $svg .= '<text class="pcw-number" x="' . ( $x + 3 ) . '" y="' . ( $y + 11 ) . '">' . $number [ "$r,$c" ] . '</text>';

      if ( $solution )
        $svg .= '<text class="pcw-letter" x="' . ( $x + $size / 2 ) . '" y="' . ( $y + 27 ) . '" text-anchor="middle">' . $h ( $letter ) . '</text>';

    }

    $svg .= '</svg>';

    // The clue lists, each clue with the length of its word.

    $lists = '';

    foreach ( [ 'Across', 'Down' ] as $way ) {

      if ( empty ( $clues [$way] ) )
        continue;

      usort ( $clues [$way], fn ( $a, $b ) => $numbers [$a] <=> $numbers [$b] );

      $lists .= '<section class="pcw-clues"><h3>' . $way . '</h3><ol>';

      foreach ( $clues [$way] as $index )
        $lists .= '<li value="' . $numbers [$index] . '">' . $h ( $placed [$index] [4] )
                . ' <span class="pcw-length">(' . $placed [$index] [5] . ')</span>'
                . ( $solution ? ' <span class="pcw-answer">' . $h ( $placed [$index] [0] ) . '</span>' : '' ) . '</li>';

      $lists .= '</ol></section>';

    }

    $note = $skipped ? '<p class="pcw-skipped">Not placed: ' . $h ( implode ( ', ', $skipped ) ) . '</p>' : '';

    return padCrosswordStyle () . '<div class="pad-crossword' . ( $solution ? ' is-solved' : '' ) . '">'
         . $svg . "<div class=\"pcw-lists\">$lists$note</div></div>";

  }

  // The id of a grid, made from the grid itself, with a number behind a second copy.

  function padCrosswordId ( $grid ) {

    static $drawn = [];

    $id = 'pad-crossword-' . substr ( md5 ( serialize ( $grid ) ), 0, 8 );

    $drawn [$id] = ( $drawn [$id] ?? 0 ) + 1;

    return ( $drawn [$id] > 1 ) ? $id . '-' . $drawn [$id] : $id;

  }

  // The look, once per page: squares, lines, numbers and letters as custom properties with
  // light-dark() defaults; the grid beside the clues where there is room, above them where
  // there is not.

  function padCrosswordStyle () {

    static $written = FALSE;

    if ( $written )
      return '';

    $written = TRUE;

    $roles = [ 'square' => [ '#ffffff', '#1f1f1d' ], 'line'   => [ '#2b2a28', '#c3c2b7' ],
               'number' => [ '#52514e', '#a3a29b' ], 'letter' => [ '#1a1a19', '#f4f3ee' ],
               'answer' => [ '#2a78d6', '#5598e7' ] ];

    $light = $both = '';

    foreach ( $roles as $role => list ( $day, $night ) ) {
      $light .= "--pad-crossword-$role:$day;";
      $both  .= "--pad-crossword-$role:light-dark($day,$night);";
    }

    return '<style>'
         . ":where(.pad-crossword){{$light}}"
         . "@supports (color:light-dark(#000,#fff)){:where(.pad-crossword){{$both}}}"
         . ':where(.pad-crossword){display:flex;flex-wrap:wrap;gap:20px 32px;align-items:flex-start}'
         . ':where(.pad-crossword .pcw-grid){max-width:100%;height:auto;flex:none}'
         . '.pad-crossword .pcw-square{fill:var(--pad-crossword-square);stroke:var(--pad-crossword-line);stroke-width:1.5}'
         . '.pad-crossword .pcw-number{fill:var(--pad-crossword-number);font:600 10px system-ui,sans-serif}'
         . '.pad-crossword .pcw-letter{fill:var(--pad-crossword-letter);font:600 19px system-ui,sans-serif}'
         . ':where(.pad-crossword .pcw-lists){display:flex;flex-wrap:wrap;gap:12px 32px;flex:1 1 220px;font-size:14px;line-height:1.45}'
         . ':where(.pad-crossword .pcw-clues){flex:1 1 200px}'
         . ':where(.pad-crossword h3){margin:0 0 6px;font-size:15px}'
         . ':where(.pad-crossword ol){margin:0;padding-left:2em}'
         . ':where(.pad-crossword li){margin:0 0 4px}'
         . ':where(.pad-crossword .pcw-length){opacity:.65}'
         . ':where(.pad-crossword .pcw-answer){color:var(--pad-crossword-answer);font-weight:600;letter-spacing:.06em}'
         . ':where(.pad-crossword .pcw-skipped){flex-basis:100%;margin:0;font-size:13px;opacity:.75}'
         . '</style>';

  }

?>
