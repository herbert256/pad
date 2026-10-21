<?php

  $inits = 'root';

  // The menu of the frame every PAD application shares (_common/_tags/padFrame.pad): the demo
  // page of every level.

  $frameMenu = [];

  foreach ( [ 'page', 'abc/page', 'abc/klm/page', 'abc/klm/xyz/page', 'def/page', 'def/klm/page' ] as $structurePage )
    $frameMenu [] = [ 'link' => "?$structurePage", 'label' => $structurePage, 'icon' => 'file',
                      'current' => $padPage == $structurePage ];

  unset ( $structurePage );

?>
