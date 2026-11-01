<?php

  // Rows of a table whose parent_id is NOT NULL DEFAULT 0: a parent 0 that is no row's value
  // is a root - Fruit and Veg - where every row was refused as under a parent that is none.
  // Where a row has the value 0 - the zone World - a parent 0 is that row, as any other.

  $cats = [
    [ 'id' => 1, 'name' => 'Fruit', 'parent' => 0 ],
    [ 'id' => 2, 'name' => 'Veg',   'parent' => '0' ],
    [ 'id' => 3, 'name' => 'Apple', 'parent' => 1 ]
  ];

  $zones = [
    [ 'code' => 0,  'name' => 'World',  'above' => '' ],
    [ 'code' => 10, 'name' => 'Europe', 'above' => 0 ]
  ];

?>
