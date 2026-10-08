<?php

  // Every page: the menu and the counts. The tag pages and the indexes draw examples
  // through {page}, which runs this file again for each: only the first run - the page that
  // was asked for - sets the frame.

  if ( isset ( $tagsFrame ) )
    return;

  $tagsFrame    = $padPage;
  $tagsCount    = count ( tagsCatalog () );
  $tagsGroupCnt = count ( tagsByGroup () );
  $tagsSearch   = '';

  $tagsMenu = [];

  foreach ( [ 'index'      => 'Home',
              'names'      => 'A-Z',
              'groups'     => 'Groups',
              'forms'      => 'Forms',
              'options'    => 'Options',
              'cheatsheet' => 'Cheat sheet' ] as $page => $label )
    $tagsMenu [] = [ 'page' => $page, 'label' => $label, 'active' => $page == $padPage ];

  $title = 'PAD tags';

?>
