<?php

  // The patterns of the engine, as the engine reads them - the files of pad/subs/images/patterns/types/
  // (padPatternKinds) - each with what its own file says of it: the comment at its top, its
  // first paragraph the text of its page, the first sentence of that its line, and the
  // example the comment holds.

  function patternsCatalog () {

    $patterns = [];

    foreach ( padPatternKinds () as $kind ) {

      list ( $text, $example ) = patternsComment ( PAD . "subs/images/patterns/types/$kind.php", $kind );

      // The line of a card: the first sentence up to its colon or dash, at most sixteen words.

      $first = rtrim ( preg_split ( '/(?<=\.)\s/', $text, 2 ) [0], '.' );
      $line  = preg_match ( '/^(.{12,}?)(?:: | - )/', $first, $match ) ? $match [1] : $first;
      $words = preg_split ( '/\s+/', $line );
      $line  = ( count ( $words ) > 16 ) ? rtrim ( implode ( ' ', array_slice ( $words, 0, 16 ) ), ',;:- ' ) . ' ...' : $line;

      $patterns [] = [ 'kind' => $kind, 'name' => ucfirst ( $kind ), 'line' => ucfirst ( str_ends_with ( $line, '...' ) ? $line : rtrim ( $line, ',;:- ' ) . '.' ),
                       'text' => ucfirst ( $text ), 'example' => $example ];

    }

    return $patterns;

  }

  // The comment at the top of a type file: its first paragraph, without the name it starts
  // with, and the first line of the paragraphs after it that is a tag - its example.

  function patternsComment ( $file, $kind ) {

    $paragraphs = [ [] ];

    foreach ( file ( $file, FILE_IGNORE_NEW_LINES ) as $line ) {
      if ( ! preg_match ( '/^\s*\/\/\s?(.*)$/', $line, $match ) ) {
        if ( $paragraphs [0] )
          break;
        continue;
      }
      if ( trim ( $match [1] ) === '' )
        $paragraphs [] = [];
      else
        $paragraphs [ count ( $paragraphs ) - 1 ] [] = $match [1];
    }

    $text    = preg_replace ( '/^' . preg_quote ( $kind, '/' ) . '\s+-\s+/', '', implode ( ' ', array_map ( 'trim', $paragraphs [0] ) ) );
    $example = '';

    foreach ( array_slice ( $paragraphs, 1 ) as $paragraph )
      foreach ( $paragraph as $one )
        if ( $example === '' and str_starts_with ( trim ( $one ), '{' ) )
          $example = trim ( $one );

    return [ $text, $example ];

  }

  // A seed asked for by a link - ?topography&seed=8 - a whole number from 0 to 999999, else 1.

  function patternsSeed ( $value ) {

    return ( is_scalar ( $value ) and preg_match ( '/^\d{1,6}$/', (string) $value ) ) ? (int) $value : 1;

  }

?>
