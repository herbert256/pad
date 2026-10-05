<?php

  $allowed = [ 'admin/*', 'reports/*.pdf' ];

  $checks = [];

  foreach ( [ 'admin/users', 'reports/may.pdf', 'reports/may.csv' ] as $path )
    $checks [] = [ 'path' => $path, 'allowed' => padStrIs ( $allowed, $path ) ? 'yes' : 'no' ];

  $card  = padStrMask ( '4111 1111 1111 1234', '*', 0, -4 );
  $email = padStrMask ( 'ann.smith@example.com', '*', 1, 8 );

?>
