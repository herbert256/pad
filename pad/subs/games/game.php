<?php

  // The shared body of game:kind tags, such as {game:memory}. The caller sets
  // $padGameKind; registered kinds are files in subs/games/types/.
  //
  // A game is played in the browser: the server checks what it is given and writes the board,
  // the game's script plays it - once per page, with the CSP nonce, as the interactive tags
  // write theirs (lib/widget.php). The words of a word game, the clues of a crossword, the
  // questions of a quiz or the picture of a nonogram stand between its tags as they are
  // written - taken before the level walks them, so they may hold braces - or come from a
  // store named by data=.

  include_once PAD . 'subs/games/_lib/game.php';

  if ( ! padGameType ( $padGameKind ) ) {
    if ( $padCheckSyntax )
      padError ( "there is no game named '" . padMakeSafe ( $padGameKind, 20 ) . "' - " . implode ( ', ', padGameTypes () ) );
    return '';
  }

  // The game played goes on the xref record, which the reference application shows: the pages
  // that play each game.

  if ( $padInfoXref ?? FALSE )
    padInfoXref ( 'game', $padGameKind );

  $padGameSource = $padContent;
  $padContent    = '';

  // The game's markup is a value: a level loops over the data a data= option named, and would
  // write the game once per row - so the level gets its one default occurrence back.

  $padGameHtml = padGameTag ( $padGameKind, $padGameSource );

  $padData [$pad] = padDefaultData ();

  return ( $padGameHtml === '' ) ? FALSE : $padGameHtml;

?>
