<?php

  $data = [
    'user'  => [ 'name' => 'Ann', 'nick' => NULL ],
    'items' => [ [ 'id' => 1, 'price' => 10 ], [ 'id' => 2 ] ],
    'a.b'   => 1
  ];

  $user        = new stdClass;
  $user->email = NULL;

  $r = json_encode ( [
    'name'     => padArrHas ( $data, 'user.name' ),
    'nullNick' => padArrHas ( $data, 'user.nick' ),
    'list'     => padArrHas ( $data, [ 'user.name', 'items.0.id' ] ),
    'string'   => padArrHas ( $data, 'user.name, user.zip' ),
    'whole'    => padArrHas ( $data, 'a.b' ),
    'starAll'  => padArrHas ( $data, 'items.*.id' ),
    'starSome' => padArrHas ( $data, 'items.*.price' ),
    'starNone' => padArrHas ( [ 'e' => [] ], 'e.*' ),
    'noKeys'   => padArrHas ( $data, [] ),
    'nullKeys' => padArrHas ( $data, NULL ),
    'onNull'   => padArrHas ( NULL, 'a' ),
    'object'   => padArrHas ( $user, 'email' ),
    'int'      => padArrHas ( [ 'x' ], 0 )
  ] );

?>
