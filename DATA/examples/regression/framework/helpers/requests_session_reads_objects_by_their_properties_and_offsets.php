<?php

  // A session may hold objects: a dot path reads a public property of an object and an
  // offset of an ArrayAccess object, as it reads an array key, and a * maps over the items
  // of either - a missing property is missing, not an error.

  padSessionPut ( 'profile', (object) [ 'name' => 'Ann', 'address' => (object) [ 'city' => 'Delft' ] ] );
  padSessionPut ( 'bag',     new ArrayObject ( [ 'size' => 'L', 'items' => [ [ 'id' => 1 ], [ 'id' => 2 ] ] ] ) );
  padSessionPut ( 'people',  [ (object) [ 'name' => 'Bob' ], (object) [ 'age' => 7 ], (object) [ 'name' => 'Eve' ] ] );

  $j1 = padSession ( 'profile.name' ) . ' ' . padSession ( 'profile.address.city' ) . ' ' . padSession ( 'profile.phone', 'no phone' );
  $j2 = padSession ( 'bag.size' ) . ' ' . json_encode ( padSession ( 'bag.items.*.id' ) ) . ' ' . padSession ( 'bag.colour', 'no colour' );
  $j3 = json_encode ( padSession ( 'people.*.name' ) );

?>
