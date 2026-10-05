<?php

  $customers = [
    [ 'id' => 3, 'name' => 'Cleo', 'country' => 'NL' ],
    [ 'id' => 1, 'name' => 'Ann',  'country' => 'BE' ],
    [ 'id' => 2, 'name' => 'Bob',  'country' => 'NL' ]
  ];

  $names   = implode ( ', ', padArrPluck ( $customers, 'name' ) );
  $choices = padArrPluck ( padArrSortBy ( $customers, 'name' ), 'name', 'id' );

  $byId    = padArrKeyBy ( $customers, 'id' );
  $two     = $byId [2] ['name'];

?>
