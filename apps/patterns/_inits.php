<?php

  // The menu of the frame every PAD application shares (_common/_tags/padFrame.pad): the home
  // page and a page per pattern - the patterns the engine has, pad/subs/patterns/types/. The
  // catalog is a list, not keyed by the patterns' names, as the toys' and the games' are.

  $patterns = patternsCatalog ();

  $frameMenu = [ [ 'link' => '?index', 'label' => 'All patterns', 'icon' => 'grid', 'current' => $padPage == 'index' ],
                 [ 'head' => 'Patterns' ] ];

  foreach ( $patterns as $one )
    $frameMenu [] = [ 'link' => '?' . $one ['kind'], 'label' => $one ['name'], 'icon' => 'layers',
                      'current' => ( $padPage == '[kind]' and ( $kind ?? '' ) == $one ['kind'] ) ];

  // The filters of {img}, the other subsystem this application shows: one page, every filter
  // on the same picture.

  $frameMenu [] = [ 'head' => 'Pictures' ];
  $frameMenu [] = [ 'link' => '?filters', 'label' => 'Image filters', 'icon' => 'image' ];

?>
