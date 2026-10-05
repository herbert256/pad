<?php

  $user = [
    'id'       => 7,
    'name'     => 'Ann',
    'email'    => 'ann@example.com',
    'password' => '$2y$12$abc',
    'login'    => [ 'ip'      => '10.0.0.1',
                    'browser' => 'Firefox' ]
  ];

  $contact = padArrOnly   ( $user, 'email, name' );
  $public  = padArrExcept ( $user, 'password, login.ip' );

  $shown   = json_encode ( $public );

?>
