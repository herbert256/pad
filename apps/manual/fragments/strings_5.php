<?php

  $carts = [];

  foreach ( [ 0, 1, 2 ] as $n )
    $carts [] = [ 'n' => $n, 'items' => padStrPlural ( 'item', $n ), 'people' => padStrPlural ( 'person', $n ) ];

  $words = [];

  foreach ( [ 'city', 'child', 'knife', 'analysis', 'sheep', 'Person', 'blogPost' ] as $word )
    $words [] = [ 'word' => $word, 'plural' => padStrPlural ( $word ), 'back' => padStrSingular ( padStrPlural ( $word ) ) ];

?>
