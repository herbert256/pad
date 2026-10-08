<?php

  // {crossword} ... {/crossword} - a crossword made from a list of words, lib/crossword.php.
  // The content is a line per word, WORD: clue; it is taken as it stands, before the level
  // walks it, so a clue may hold braces. The words are laid out across and down so they
  // cross, the same way every time; the grid is drawn empty with its numbers, beside the
  // Across and Down clues with the length of each word. solution writes the letters in and
  // the answers behind the clues, title= names the grid. A word that crosses no other is
  // left out and named under the clues.

  if ( ! $padPair [$pad] ) {
    if ( $padCheckSyntax )
      padError ( "the pair {crossword} never closes - its content is the words, WORD: clue" );
    return '';
  }

  $padCrosswordWords = padCrosswordWords ( $padContent );
  $padContent        = '';

  if ( is_string ( $padCrosswordWords ) or ! $padCrosswordWords ) {
    if ( $padCheckSyntax )
      padError ( $padCrosswordWords ? "in the crossword, $padCrosswordWords" : 'the crossword has no words - write a line per word, WORD: clue' );
    return '';
  }

  list ( $padCrosswordPlaced, $padCrosswordSkipped ) = padCrosswordLay ( $padCrosswordWords );

  return padCrossword ( $padCrosswordPlaced, $padCrosswordSkipped, (bool) padTagParm ( 'solution', FALSE ), (string) padTagParm ( 'title' ) );

?>
