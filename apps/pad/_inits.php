<?php

  // The menu of the frame every PAD application shares (_common/_tags/padFrame.pad): this
  // application's two pages.

  $frameMenu = [
    [ 'link' => '?index', 'label' => 'Home',        'icon' => 'home', 'current' => $padPage == 'index' ],
    [ 'link' => '?hello', 'label' => 'Hello World', 'icon' => 'play', 'current' => $padPage == 'hello' ]
  ];

?>
