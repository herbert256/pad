<?php

  // Only the outer page sets up the frame; live examples run these inits again.
  if ( isset ( $subsystemsFrame ) ) return;

  $subsystemsFrame = $padPage;
  $subsystemsCount = count ( subsystemsCatalog () );
  $subsystemsSearch = '';
  $frameMenu = [];

  foreach ( [ 'index' => [ 'Home', 'home' ], 'names' => [ 'A-Z', 'list' ],
              'groups' => [ 'Groups', 'grid' ], 'cheatsheet' => [ 'Cheat sheet', 'file' ] ] as $subsystemsPage => [ $subsystemsLabel, $subsystemsIcon ] )
    $frameMenu [] = [ 'link' => "?$subsystemsPage", 'label' => $subsystemsLabel, 'icon' => $subsystemsIcon, 'current' => $subsystemsPage == $padPage ];

  $title = 'PAD subsystems';

?>
