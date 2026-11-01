<?php

  // scramble - the letters of a word, shuffled: click them in the right order - or type them -
  // to spell the word again, a point for each one found; skip shows it and goes on. The
  // words come between the tags, one a line, as a list, or by data=; without them a list
  // built in. rounds= how many words a game has, 10 unless given (1 to 50).

  return padGame ( 'scramble', 'Word scramble', [
    'words'  => padGameWords ( padGameList () ),
    'rounds' => padGameInt ( 'rounds', 10, 1, 50 )
  ] );

?>
