<?php

  // The games of the engine, as the engine reads them - the files of pad/subs/games/types/
  // (padGameTypes) - each with what its own file says of it: the comment at its top, its
  // name the word before the dash, its line the first sentence after it.

  function gamesCatalog () {

    $games = [];

    foreach ( padGameTypes () as $game ) {

      $comment = [];

      foreach ( file ( PAD . "subs/games/types/$game.php", FILE_IGNORE_NEW_LINES ) as $line ) {
        if ( preg_match ( '/^\s*\/\/\s?(.*)$/', $line, $match ) ) {
          if ( trim ( $match [1] ) === '' or str_starts_with ( ltrim ( $match [1] ), '{' ) )
            break;
          $comment [] = trim ( $match [1] );
        } elseif ( $comment )
          break;
      }

      // The comment as a reader of the page wants it: without the name it starts with and the
      // engine files it points at, its first letter a capital.

      $text = preg_replace ( '/^' . preg_quote ( $game, '/' ) . '\s+-\s+/', '', implode ( ' ', $comment ) );
      $text = preg_replace ( '/\s*\([a-z_\/]+\.php\)/', '', $text );
      $text = ucfirst ( $text );

      // The line of a card: the first sentence up to its colon - "A crossword made from a list
      // of words" - or past it when what stands before only restates the name, in fewer than
      // five words: "Bulls and cows: find the secret number ...". At most twenty words.

      $first = rtrim ( preg_split ( '/(?<=\.)\s/', $text, 2 ) [0], '.' );

      if ( preg_match ( '/^([^:;]+):\s*(.+)$/', $first, $match ) )
        $first = ( str_word_count ( $match [1] ) >= 5 ) ? $match [1] : ucfirst ( $match [2] );

      $words = preg_split ( '/\s+/', $first );
      $line  = ( count ( $words ) > 20 ) ? rtrim ( implode ( ' ', array_slice ( $words, 0, 20 ) ), ',;:- ' ) . ' ...' : rtrim ( $first, ',;:- ' ) . '.';

      $games [$game] = [ 'game' => $game, 'name' => gamesName ( $game ), 'line' => $line, 'text' => $text ];

    }

    return $games;

  }

  // The name a game is shown under: the one the game gives itself (padGame's second
  // argument, in the type file), else its file name.

  function gamesName ( $game ) {

    $php = (string) @file_get_contents ( PAD . "subs/games/types/$game.php" );

    return preg_match ( "/padGame \( '$game', '([^']+)'/", $php, $match ) ? $match [1] : ucfirst ( $game );

  }

  // The template a game is played with here: _games/<game>.pad when the game wants content -
  // the words of a crossword, the questions of a quiz - else the tag alone.

  function gamesTemplate ( $game ) {

    $file = APP . "_games/$game.pad";

    return is_file ( $file ) ? trim ( file_get_contents ( $file ) ) : "{game:$game/}";

  }

?>
