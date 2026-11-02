<?php

  // The toys of the engine, as the engine reads them - the files of pad/subs/toys/types/
  // (padToyTypes) - each with what its own file says of it: the comment at its top, its name
  // the word before the dash, its line the first sentence after it.

  function toysCatalog () {

    $toys = [];

    foreach ( padToyTypes () as $toy ) {

      $comment = [];

      foreach ( file ( PAD . "subs/toys/types/$toy.php", FILE_IGNORE_NEW_LINES ) as $line ) {
        if ( preg_match ( '/^\s*\/\/\s?(.*)$/', $line, $match ) ) {
          if ( trim ( $match [1] ) === '' or str_starts_with ( ltrim ( $match [1] ), '{' ) )
            break;
          $comment [] = trim ( $match [1] );
        } elseif ( $comment )
          break;
      }

      // The comment as a reader of the page wants it: without the name it starts with and the
      // engine files it points at, its first letter a capital.

      $text = preg_replace ( '/^' . preg_quote ( $toy, '/' ) . '\s+-\s+/', '', implode ( ' ', $comment ) );
      $text = preg_replace ( '/\s*\([a-z_\/]+\.php\)/', '', $text );
      $text = ucfirst ( $text );

      // The line of a card: the first sentence up to its colon - "Conway's Game of Life" is
      // only the name again, so what follows it is the line - or the whole sentence when what
      // stands before the colon says more, in five words or more. At most twenty words.

      $first = rtrim ( preg_split ( '/(?<=\.)\s/', $text, 2 ) [0], '.' );

      if ( preg_match ( '/^([^:;]+):\s*(.+)$/', $first, $match ) )
        $first = ( str_word_count ( $match [1] ) >= 5 ) ? $match [1] : ucfirst ( $match [2] );

      $words = preg_split ( '/\s+/', $first );
      $line  = ( count ( $words ) > 20 ) ? rtrim ( implode ( ' ', array_slice ( $words, 0, 20 ) ), ',;:- ' ) . ' ...' : rtrim ( $first, ',;:- ' ) . '.';

      $toys [$toy] = [ 'toy' => $toy, 'name' => toysName ( $toy ), 'line' => $line, 'text' => $text ];

    }

    return $toys;

  }

  // The name a toy is shown under: the one the toy gives itself (padToy's second argument, in
  // the type file), else its file name.

  function toysName ( $toy ) {

    $php = (string) @file_get_contents ( PAD . "subs/toys/types/$toy.php" );

    return preg_match ( "/padToy \( '$toy', (?:'([^']+)'|\"([^\"]+)\")/", $php, $match ) ? ( $match [2] ?? '' ) . $match [1] : ucfirst ( $toy );

  }

  // The template a toy is shown with here: _toys/<toy>.pad when the toy is shown with options
  // or content - the glider gun of the Game of Life, the rules of a plant - else the tag
  // alone.

  function toysTemplate ( $toy ) {

    $file = APP . "_toys/$toy.pad";

    return is_file ( $file ) ? trim ( file_get_contents ( $file ) ) : "{toy:$toy/}";

  }

?>
