<?php

  // The title in the frame's bar, from the page's name - a page's own PHP sets a better one.

  $title = ucfirst ( $padPage );

  if ( $padPage == 'index' )
    $title = 'Welcome';

  // The menu of the frame every PAD application shares (_common/_tags/padFrame.pad): a link
  // per example, as _data/navigate.json lists them - the page, its text and its icon.

  $frameMenu = [];

  foreach ( json_decode ( (string) file_get_contents ( APP . '_data/navigate.json' ), TRUE ) ?: [] as $demoLink )
    $frameMenu [] = [ 'link' => "?{$demoLink ['page']}", 'label' => $demoLink ['text'], 'icon' => $demoLink ['icon'] ?? '',
                      'current' => $padPage == $demoLink ['page'] ];

  unset ( $demoLink );

?>
