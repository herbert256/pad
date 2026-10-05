<?php

  $row = [ 'id' => 7, 'name' => 'Ann', 'email' => 'ann@example.com', 'password' => 'x', 'meta' => [ 'ip' => '10.0.0.1', 'agent' => 'curl' ] ];

  $only     = json_encode ( padArrOnly ( $row, 'email, id, nothing' ) );
  $onlyList = json_encode ( padArrOnly ( $row, [ 'name' ] ) );
  $onlyNull = json_encode ( padArrOnly ( [ 'a' => NULL ], 'a' ) );
  $onlyInt  = json_encode ( padArrOnly ( [ 'x', 'y', 'z' ], [ 2, 0 ] ) );
  $onlyNone = json_encode ( padArrOnly ( NULL, 'a' ) );

  $except     = json_encode ( padArrExcept ( $row, 'password, meta.ip' ) );
  $exceptNone = json_encode ( padArrExcept ( 'text', 'a' ) );

  $user           = new stdClass;
  $user->name     = 'Bob';
  $user->password = 'secret';
  $users          = [ $user ];

  $onlyObject = json_encode ( padArrOnly ( $user, 'name' ) );
  $public     = json_encode ( padArrExcept ( $users, '*.password' ) );
  $untouched  = json_encode ( $users );

?>
