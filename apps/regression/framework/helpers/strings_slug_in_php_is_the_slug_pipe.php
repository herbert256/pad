<?php

  $texts = [ 'Crème Brûlée & Co.', ' Hello, World! ', 'ÆØÅ æøå ß', '--a--b--', '' ];

  $slugs = [];

  foreach ( $texts as $text )
    $slugs [] = [ 'text' => $text, 'dash' => padStrSlug ( $text ), 'under' => padStrSlug ( $text, '_' ) ];

  $r = json_encode ( [ padStrSlug ( NULL ), padStrSlug ( 0 ), padStrSlug ( FALSE ), padStrSlug ( 12.5 ), padStrSlug ( 'a b c', '$0' ), padStrSlug ( 'xavier max', 'x' ) ] );

?>
