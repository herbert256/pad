<?php

  // Every page: the menu and the counts. The type pages and the indexes draw examples through
  // {pad:page}, which runs this file again for each: only the first run - the page that was asked
  // for - sets the frame.

  if ( isset ( $typesFrame ) )
    return;

  $typesFrame    = $padPage;
  $typesCount    = count ( typesCatalog () );
  $typesSearch   = '';

  $frameMenu = [];

  foreach ( [ 'index'      => [ 'Home',        'home'   ],
              'names'      => [ 'A-Z',         'list'   ],
              'order'      => [ 'Type prefixes', 'repeat' ],
              'groups'     => [ 'Groups',      'grid'   ],
              'cheatsheet' => [ 'Cheat sheet', 'file'   ] ] as $typesPage => [ $typesLabel, $typesIcon ] )
    $frameMenu [] = [ 'link' => "?$typesPage", 'label' => $typesLabel, 'icon' => $typesIcon, 'current' => $typesPage == $padPage ];

  $title = 'PAD types';

?>
