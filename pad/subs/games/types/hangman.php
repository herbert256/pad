<?php

  // hangman - guess the word a letter at a time before the drawing is finished: a letter that
  // is in the word shows wherever it stands, one that is not adds a line to the gallows. The
  // words are written between the tags, one a line, or given as a list - 'harbour, lighthouse'
  // - or by data=, a word picked each game; without them a word of a list built in. tries=
  // how many wrong letters end it, 6 unless given (3 to 10).
  //
  //   {hangman}
  //     template
  //     expression
  //   {/hangman}

  $padHangWords = padGameWords ( padGameList () );

  return padGame ( 'hangman', 'Hangman', [
    'words' => $padHangWords,
    'tries' => padGameInt ( 'tries', 6, 3, 10 )
  ] );

?>
