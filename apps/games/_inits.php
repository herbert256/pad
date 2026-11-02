<?php

  // The menu of the frame every PAD application shares (_common/_tags/padFrame.pad): the home
  // page and a page per game - the games the engine has, pad/subs/games/types/.

  // A list, not keyed by name: an array of the page with a key named like a game is found
  // before the game when the name is looked up as a tag - {game:hangman} looped over the entry.

  $games = array_values ( gamesCatalog () );

  $frameMenu = [ [ 'link' => '?index', 'label' => 'Games and toys', 'icon' => 'grid', 'current' => $padPage == 'index' ],
                 [ 'head' => 'Games' ] ];

  foreach ( $games as $one )
    $frameMenu [] = [ 'link' => '?' . $one ['game'], 'label' => $one ['name'], 'icon' => 'play',
                      'current' => ( $padPage == '[game]' and ( $game ?? '' ) == $one ['game'] ) ];

  $toys = array_values ( toysCatalog () );
  $frameMenu [] = [ 'head' => 'Toys and simulations' ];
  $frameMenu [] = [ 'link' => '?toys/index', 'label' => 'All toys', 'icon' => 'sparkle', 'current' => $padPage == 'toys/index' ];
  foreach ( $toys as $one )
    $frameMenu [] = [ 'link' => '?toys/' . $one ['toy'], 'label' => $one ['name'], 'icon' => 'play',
                      'current' => $padPage == 'toys/[toy]' && ( $toy ?? '' ) == $one ['toy'] ];

  include_once COMMON . '_lib/showcaseExamples.php';
  $showcaseFamilies = showcaseExampleFamilies ();
  $frameMenu = array_merge ( $frameMenu, showcaseExampleMenu ( $showcaseFamilies, $padPage ) );

?>
