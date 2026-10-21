<?php

  // The menu of the frame every PAD application shares (_common/_tags/padFrame.pad): the
  // list, which the browse pages belong to.

  $frameMenu = [
    [ 'link' => '?index', 'label' => 'Applications', 'icon' => 'grid', 'current' => $padPage == 'index' or $padPage == 'browse' ]
  ];

?>
