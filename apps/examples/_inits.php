<?php

  // The menu of the frame every PAD application shares (_common/_tags/padFrame.pad): the
  // search, which the view of one example belongs to.

  $frameMenu = [
    [ 'link' => '?index', 'label' => 'Search', 'icon' => 'search', 'current' => $padPage == 'index' or $padPage == 'show' ]
  ];

?>
