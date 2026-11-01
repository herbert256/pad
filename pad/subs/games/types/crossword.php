<?php

  // crossword - a crossword made from a list of words: a line per word between the tags, WORD:
  // clue. The words are laid across and down so they cross, the same way every time
  // (subs/games/_lib/crossword.php); type the answers into the squares - a click on the square
  // that is selected, or on a clue, turns the direction - check marks the wrong letters and
  // reveal shows a word or all of them. paper draws the empty grid beside the clues instead,
  // to print, solution the grid filled in with the answers behind the clues; title= names
  // it. A word that crosses no other is left out and named under the clues. By data= a row
  // is a word and its clue.
  //
  //   {crossword}
  //     ECHO: Writes a value without the sanitize chain
  //     PIPE: The | that hands a value to a function
  //   {/crossword}

  $padCrosswordSource = padGameSource ();

  if ( trim ( $padCrosswordSource ) === '' and ( $padCrosswordRows = padGameRows () ) !== NULL )
    foreach ( (array) $padCrosswordRows as $padCrosswordRow ) {
      $padCrosswordRow = is_object ( $padCrosswordRow ) ? padToArray ( $padCrosswordRow ) : $padCrosswordRow;
      if ( is_array ( $padCrosswordRow ) and isset ( $padCrosswordRow ['word'], $padCrosswordRow ['clue'] ) )
        $padCrosswordSource .= $padCrosswordRow ['word'] . ': ' . $padCrosswordRow ['clue'] . "\n";
      elseif ( $GLOBALS ['padCheckSyntax'] )
        padError ( "a row of the crossword data has no word and clue - its fields are word and clue" );
    }

  $padCrosswordWords = padCrosswordWords ( $padCrosswordSource );

  if ( is_string ( $padCrosswordWords ) or ! $padCrosswordWords ) {
    if ( $GLOBALS ['padCheckSyntax'] )
      padError ( $padCrosswordWords ? "in the crossword, $padCrosswordWords" : 'the crossword has no words - write a line per word, WORD: clue' );
    return '';
  }

  list ( $padCrosswordPlaced, $padCrosswordSkipped ) = padCrosswordLay ( $padCrosswordWords );

  if ( padGameFlag ( 'solution' ) or padGameFlag ( 'paper' ) )
    return padCrossword ( $padCrosswordPlaced, $padCrosswordSkipped, padGameFlag ( 'solution' ), padParmText ( padTagParm ( 'title' ) ) );

  return padGame ( 'crossword', 'Crossword', padCrosswordPlay ( $padCrosswordPlaced, $padCrosswordSkipped ) );

?>
