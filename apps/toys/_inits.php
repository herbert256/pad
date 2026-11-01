<?php

  // The menu of the frame every PAD application shares (_common/_tags/padFrame.pad): the home
  // page and a page per toy - the toys the engine has, pad/subs/toys/types/. The catalog is a list,
  // not keyed by the toys' names: a page's array with a key life made a bare {life/} in the
  // template that array's row instead of the toy.

  $toys = array_values ( toysCatalog () );

  $frameMenu = [ [ 'link' => '?index', 'label' => 'All toys', 'icon' => 'grid', 'current' => $padPage == 'index' ],
                 [ 'head' => 'Toys' ] ];

  foreach ( $toys as $one )
    $frameMenu [] = [ 'link' => '?' . $one ['toy'], 'label' => $one ['name'], 'icon' => 'play',
                      'current' => ( $padPage == '[toy]' and ( $toy ?? '' ) == $one ['toy'] ) ];

?>
