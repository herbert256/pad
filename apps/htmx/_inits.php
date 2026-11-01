<?php

  // Every page: the title the frame's bar shows unless the page sets its own, the menu of
  // the frame every PAD application shares (_common/_tags/padFrame.pad) - the home page, the
  // guide and the examples under the heading of their group, as _data/examples.json has
  // them - the header every htmx request sends - the session's CSRF token, which a post
  // needs - and the count of the cart badge in the bar.

  $title = 'PAD + htmx';

  $frameMenu = [ [ 'link' => '?index', 'label' => 'Examples', 'icon' => 'home' ],
                 [ 'link' => '?guide', 'label' => 'Guide',    'icon' => 'book-open' ] ];

  foreach ( htmxGroups () as $htmxGroup ) {

    $frameMenu [] = [ 'head' => $htmxGroup ['title'] ];

    foreach ( $htmxGroup ['examples'] as $htmxOne )
      $frameMenu [] = [ 'link' => '?' . $htmxOne ['page'], 'label' => $htmxOne ['title'], 'icon' => '' ];

  }

  unset ( $htmxGroup, $htmxOne );

  $htmxHeaders = [ 'X-CSRF-Token' => padCsrfToken () ];

  $cartCount = htmxCart ( padSession ( 'htmxCart', [] ) ) ['count'];

?>
