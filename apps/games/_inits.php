<?php

  // The menu of the frame every PAD application shares (_common/_tags/padFrame.pad): the home
  // page and a page per game - the games the engine has, pad/subs/games/types/.

  // A list, not keyed by name: an array of the page with a key named like a game is found
  // before the game when the name is looked up as a tag - {hangman} looped over the entry.

  $games = array_values ( gamesCatalog () );

  $frameMenu = [ [ 'link' => '?index', 'label' => 'All games', 'icon' => 'grid', 'current' => $padPage == 'index' ],
                 [ 'head' => 'Games' ] ];

  foreach ( $games as $one )
    $frameMenu [] = [ 'link' => '?' . $one ['game'], 'label' => $one ['name'], 'icon' => 'play',
                      'current' => ( $padPage == '[game]' and ( $game ?? '' ) == $one ['game'] ) ];

?>
