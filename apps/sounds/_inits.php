<?php

  // The menu of the frame every PAD application shares (_common/_tags/padFrame.pad): the
  // soundboard - every sound the engine has, the files of pad/subs/sounds/types/ - and the
  // page of the ways to play one.

  $frameMenu = [ [ 'link' => '?index', 'label' => 'Soundboard',   'icon' => 'grid', 'current' => $padPage == 'index' ],
                 [ 'link' => '?ways',  'label' => 'Ways to play', 'icon' => 'play', 'current' => $padPage == 'ways' or str_starts_with ( $padPage, 'ways/' ) ] ];

?>
