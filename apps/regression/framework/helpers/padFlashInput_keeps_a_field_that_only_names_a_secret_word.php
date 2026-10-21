<?php

  $_SERVER ['REQUEST_METHOD'] = 'POST';
  $_POST = [ 'pin' => '1', 'backup_frequency' => 'daily', 'token_name' => 'CI deploy', 'recovery_plan' => 'call',
             'boarding_pass' => 'B12', 'salt' => 'sea', 'api_key' => 'k1', 'access_token' => 't1', 'otp' => '123456' ];

  padFlashInput ();

  $flashed = [];

  foreach ( array_keys ( $_POST ) as $flashKey )
    $flashed [] = "$flashKey=[" . padOld ( $flashKey ) . ']';

  $flashed = implode ( ' ', $flashed );

?>
