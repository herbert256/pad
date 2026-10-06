<?php

  $customers = [
    [ 'id' => 3, 'name' => 'Cleo', 'city' => [ 'name' => 'Ede' ] ],
    [ 'id' => 1, 'name' => 'Ann',  'city' => [ 'name' => 'Leiden' ] ],
    [ 'id' => 2, 'name' => 'Bob' ]
  ];

  $user       = new stdClass;
  $user->name = 'Dirk';

  $names   = json_encode ( padArrPluck ( $customers, 'name' ) );
  $byId    = json_encode ( padArrPluck ( $customers, 'name', 'id' ) );
  $cities  = json_encode ( padArrPluck ( $customers, 'city.name', 'name' ) );
  $made    = json_encode ( padArrPluck ( $customers, fn ( $row ) => strtolower ( $row ['name'] ) ) );
  $objects = json_encode ( padArrPluck ( [ $user ], 'name' ) );
  $none    = json_encode ( padArrPluck ( NULL, 'name' ) );
  $flags   = json_encode ( padArrPluck ( [ [ 'on' => TRUE, 'v' => 'yes' ], [ 'on' => FALSE, 'v' => 'no' ], [ 'on' => NULL, 'v' => 'unset' ] ], 'v', 'on' ) );
  $later   = json_encode ( padArrPluck ( [ [ 'k' => 'a', 'v' => 1 ], [ 'k' => 'a', 'v' => 2 ] ], 'v', 'k' ) );

?>
