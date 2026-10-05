<?php

  $columns = [];

  foreach ( [ 'created_at', 'orderTotal', 'customer-name', 'HTMLParser' ] as $column )
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
