<?php

  // A game, by the name in the address - ?memory, /games/memory: one of the engine's games,
  // else a page not found. Its template is shown beside it, coloured, and played.

  if ( ! isset ( $games [$game] ) )
    padAbort ( 404 );

  $one      = $games [$game];
  $title    = $one ['name'];
  $text     = $one ['text'];
  $template = gamesTemplate ( $game );
  $source   = padHighlightTokens ( $template, 'pad' );

?>
