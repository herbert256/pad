<?php

  // The menu of the frame every PAD application shares (_common/_tags/padFrame.pad): the home
  // page and a page per kind - the printables the engine has, pad/subs/printables/types/. The
  // catalog is a list, not keyed by the kinds' names.

  $printables = array_values ( printablesCatalog () );

  $frameMenu = [ [ 'link' => '?index', 'label' => 'All printables', 'icon' => 'grid', 'current' => $padPage == 'index' ],
                 [ 'head' => 'Printables' ] ];

  foreach ( $printables as $one )
    $frameMenu [] = [ 'link' => '?' . $one ['kind'], 'label' => $one ['name'], 'icon' => 'file-text',
                      'current' => ( $padPage == '[kind]' and ( $kind ?? '' ) == $one ['kind'] ) ];

?>
