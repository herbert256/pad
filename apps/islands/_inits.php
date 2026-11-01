<?php

  // The menu of the frame every PAD application shares (_common/_tags/padFrame.pad): the
  // examples' overview and the guide, then the examples of each group of _data/examples.json
  // under its heading - a link lights when it names the page shown. The title the bar shows
  // is the page's own; one that sets none is named after its file.

  $title = ucfirst ( basename ( $padPage ) );

  $frameMenu = [ [ 'link' => '?index', 'label' => 'All examples', 'icon' => 'grid' ],
                 [ 'link' => '?guide', 'label' => 'Guide',        'icon' => 'book-open' ] ];

  foreach ( islandsGroups () as $islandsGroup ) {

    $frameMenu [] = [ 'head' => $islandsGroup ['title'] ];

    foreach ( $islandsGroup ['examples'] as $islandsOne )
      $frameMenu [] = [ 'link' => '?' . $islandsOne ['page'], 'label' => $islandsOne ['title'], 'icon' => '' ];

  }

  unset ( $islandsGroup, $islandsOne );

?>
