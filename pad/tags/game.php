<?php

  // {game 'memory', pairs=6/} - a little game played in the browser, of the kind its
  // parameter names: a kind is a file of subs/games/types/, set up by the games subsystem in
  // subs/games/, the same game as the game: type writes it, {game:memory pairs=6/}.

  $padGameKind = strtolower ( trim ( (string) $padParm ) );

  return include PAD . 'subs/games/game.php';

?>
