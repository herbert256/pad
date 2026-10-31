<?php

  // A game, by the name in the address - ?memory, /games/memory: one of the engine's games,
  // else a page not found. Its template is shown beside it, coloured, and played.

  $one = array_values ( array_filter ( $games, fn ( $entry ) => $entry ['game'] === $game ) ) [0] ?? NULL;

  if ( ! $one )
    padAbort ( 404 );

  $title    = $one ['name'];
  $text     = $one ['text'];
  $template = gamesTemplate ( $game );
  $source   = padHighlightTokens ( $template, 'pad' );

?>
