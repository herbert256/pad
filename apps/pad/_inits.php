<?php

  // The frame every PAD application shares (_common/_tags/padFrame.pad) instead of the
  // _common wrapper, with this application's two pages as its menu.

  $commonFramed = TRUE;

  $frameMenu = [
    [ 'link' => '?index', 'label' => 'Home',        'icon' => 'home', 'current' => $padPage == 'index' ],
    [ 'link' => '?hello', 'label' => 'Hello World', 'icon' => 'play', 'current' => $padPage == 'hello' ]
  ];

?>
