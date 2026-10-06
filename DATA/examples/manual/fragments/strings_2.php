<?php

  $names = [ 'created_at', 'orderTotal',
             'customer-name', 'HTMLParser' ];

  $columns = [];

  foreach ( $names as $column )
    $columns [] = [
      'column'   => $column,
      'camel'    => padStrCamel    ( $column ),
      'studly'   => padStrStudly   ( $column ),
      'snake'    => padStrSnake    ( $column ),
      'kebab'    => padStrKebab    ( $column ),
      'headline' => padStrHeadline ( $column )
    ];

  $sentence = padStrTitle ( 'the quick brown fox' );

?>
