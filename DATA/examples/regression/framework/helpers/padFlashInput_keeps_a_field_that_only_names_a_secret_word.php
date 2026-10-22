<?php

  $_SERVER ['REQUEST_METHOD'] = 'POST';
  $_POST = [ 'pinned' => '1', 'backup_frequency' => 'daily', 'token_name' => 'CI deploy', 'recovery_plan' => 'call',
             'passenger_count' => '2', 'salt' => 'sea', 'api_key' => 'k1', 'access_token' => 't1', 'otp' => '123456' ];

  padFlashInput ();

  $flashed = [];

  foreach ( array_keys ( $_POST ) as $flashKey )
    $flashed [] = "$flashKey=[" . padOld ( $flashKey ) . ']';

  $flashed = implode ( ' ', $flashed );

?>
