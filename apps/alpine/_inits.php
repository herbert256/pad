<?php

  // Every page: the title the frame's bar shows unless the page sets its own, and the menu
  // of the frame every PAD application shares (_common/_tags/padFrame.pad) - the home page,
  // the guide and the examples under the heading of their group, as _data/examples.json has
  // them.

  $title = 'PAD + Alpine';

  $frameMenu = [ [ 'link' => '?index', 'label' => 'Examples', 'icon' => 'home' ],
                 [ 'link' => '?guide', 'label' => 'Guide',    'icon' => 'book-open' ] ];

  foreach ( alpineGroups () as $alpineGroup ) {

    $frameMenu [] = [ 'head' => $alpineGroup ['title'] ];

    foreach ( $alpineGroup ['examples'] as $alpineOne )
      $frameMenu [] = [ 'link' => '?' . $alpineOne ['page'], 'label' => $alpineOne ['title'], 'icon' => '' ];

  }

  unset ( $alpineGroup, $alpineOne );

?>
