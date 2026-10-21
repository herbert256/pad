<?php

  // Every page: the menu and the counts. The tag pages and the indexes draw examples
  // through {page}, which runs this file again for each: only the first run - the page that
  // was asked for - sets the frame.

  if ( isset ( $tagsFrame ) )
    return;

  $tagsFrame    = $padPage;
  $tagsCount    = count ( tagsCatalog () );
  $tagsGroupCnt = count ( array_unique ( array_column ( tagsCatalog (), 'group' ) ) );
  $tagsSearch   = '';

  // The menu of the frame (_common/_tags/padFrame.php). The pages show their own headings,
  // and the frame's bar holds the search box instead of the title.

  $frameMenu = [];

  foreach ( [ 'index'      => [ 'Home',        'home'   ],
              'names'      => [ 'A-Z',         'list'   ],
              'groups'     => [ 'Groups',      'grid'   ],
              'forms'      => [ 'Forms',       'code'   ],
              'options'    => [ 'Options',     'filter' ],
              'cheatsheet' => [ 'Cheat sheet', 'file'   ] ] as $tagsPage => [ $tagsLabel, $tagsIcon ] )
    $frameMenu [] = [ 'link' => "?$tagsPage", 'label' => $tagsLabel, 'icon' => $tagsIcon, 'current' => $tagsPage == $padPage ];

  $title = 'PAD tags';

?>
