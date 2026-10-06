<?php

  $data = [
    'user'  => [ 'name' => 'Ann', 'address' => [ 'city' => 'Leiden' ], 'nick' => NULL ],
    'items' => [ [ 'id' => 1, 'price' => 10 ], [ 'id' => 2, 'price' => 25 ] ],
    'a.b'   => 'one key with a dot'
  ];

  $city     = padArrGet ( $data, 'user.address.city' );
  $price    = padArrGet ( $data, 'items.1.price' );
  $whole    = padArrGet ( $data, 'a.b' );
  $missing  = padArrGet ( $data, 'user.zip', 'no zip' );
  $lazy     = padArrGet ( $data, 'user.zip', fn () => 'made when needed' );
  $nick     = json_encode ( padArrGet ( $data, 'user.nick', 'not used' ) );
  $all      = json_encode ( padArrGet ( [ 1, 2 ], NULL ) );
  $index    = padArrGet ( [ 'x', 'y' ], 1 );
  $deeper   = padArrGet ( $data, 'user.name.first', 'a string has no keys' );
  $onNull   = padArrGet ( NULL, 'a', 'null' );
  $onText   = padArrGet ( 'abc', 0, 'text' );
  $onZero   = padArrGet ( 0, 'a', 'zero' );
  $onFalse  = padArrGet ( FALSE, 'a', 'false' );
  $onEmpty  = padArrGet ( [], 'a', 'empty' );
  $emptyKey = padArrGet ( [ '' => 'the empty key' ], '' );
  $unicode  = padArrGet ( [ 'ü' => [ 'é' => 'ok' ] ], 'ü.é' );

?>
