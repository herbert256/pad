<?php

  $_SERVER ['REQUEST_METHOD'] = 'POST';
  $_POST = [ 'name' => 'ann', 'current_password' => 's1', 'new_password' => 's2', 'new_password_confirmation' => 's2',
             'pwd' => 's4', 'user' => [ 'password' => 's3', 'email' => 'ann@example.com' ] ];

  padFlashInput ();

  $flashed = [];

  foreach ( [ 'name', 'current_password', 'new_password', 'new_password_confirmation', 'pwd', 'user.password', 'user.email' ] as $flashKey )
    $flashed [] = "$flashKey=[" . padOld ( $flashKey ) . ']';

  $flashed = implode ( ' ', $flashed );

?>
