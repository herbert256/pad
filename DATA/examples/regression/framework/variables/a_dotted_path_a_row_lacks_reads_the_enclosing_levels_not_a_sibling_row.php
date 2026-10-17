<?php

  // Rows without the dotted path read it from the enclosing levels and the page's variables,
  // never from another row: every row printed Bob, the row's customer.address.city stood in
  // for the page's $address.city, a row without purchases got the first row's.

  $user    = [ 'name' => 'Ann' ];
  $rows    = [ [ 'a' => 1 ], [ 'a' => 3, 'user' => [ 'name' => 'Bob' ] ], [ 'a' => 5 ] ];
  $address = [ 'city' => 'Amsterdam' ];
  $orders  = [ [ 'id' => 1, 'customer' => [ 'name' => 'X', 'address' => [ 'city' => 'Paris' ] ] ], [ 'id' => 2, 'customer' => [ 'name' => 'Y' ] ] ];
  $users   = [ [ 'name' => 'A', 'bought' => [ [ 'id' => 1 ] ] ], [ 'name' => 'B' ], [ 'name' => 'C', 'bought' => [ [ 'id' => 3 ] ] ] ];
  $a       = [ 'n' => 2 ];
  $items   = [ [ 'name' => 'x', 'a' => [ 'n' => 1 ] ], [ 'name' => 'y' ] ];

?>
