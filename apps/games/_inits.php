<?php

  // The menu of the frame every PAD application shares (_common/_tags/padFrame.pad): the home
  // page and a page per game - the games the engine has, pad/games/types/.

  $games = gamesCatalog ();

  $frameMenu = [ [ 'link' => '?index', 'label' => 'All games', 'icon' => 'grid', 'current' => $padPage == 'index' ],
                 [ 'head' => 'Games' ] ];

  foreach ( $games as $one )
    $frameMenu [] = [ 'link' => '?' . $one ['game'], 'label' => $one ['name'], 'icon' => 'play',
                      'current' => ( $padPage == '[game]' and ( $game ?? '' ) == $one ['game'] ) ];

?>
