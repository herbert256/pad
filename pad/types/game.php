<?php

  // Type handler for a game: {game:memory pairs=6/}, or a bare tag naming a file of
  // games/types/ when nothing else has the name, {sudoku/}. The game is the tag's name, and
  // it is set up as {game 'memory'} sets it up, by games/game.php - run as a built-in tag's
  // PHP half is run, types/_go/tag.php.

  $padGameKind = $padTag [$pad];

  $padTagGo = PAD . 'games/game';

  return include PAD . 'types/_go/tag.php';

?>
