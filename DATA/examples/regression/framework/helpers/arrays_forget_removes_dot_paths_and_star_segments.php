<?php

  $data = [
    'user'  => [ 'name' => 'Ann', 'address' => [ 'city' => 'Leiden' ] ],
    'items' => [ [ 'id' => 1, 'price' => 10 ], [ 'id' => 2, 'price' => 25 ] ],
    'a.b'   => 'one key'
  ];

  $a = $data;
  padArrForget ( $a, 'user.address, items.0, a.b' );
  $several = json_encode ( $a );

  $b = $data;
  $answer = json_encode ( padArrForget ( $b, [ 'items.*.price' ] ) ['items'] );

  $c = $data;
  padArrForget ( $c, 'items.*' );
  $emptied = json_encode ( $c ['items'] );

  $d = $data;
  padArrForget ( $d, 'nothing.here' );
  $same = json_encode ( $d === $data );

  $e = NULL;
  $onNull = json_encode ( padArrForget ( $e, 'a' ) );

  $f       = new stdClass;
  $f->name = 'Bob';
  $f->pass = 'secret';
  padArrForget ( $f, 'pass' );
  $object = json_encode ( $f );

?>
