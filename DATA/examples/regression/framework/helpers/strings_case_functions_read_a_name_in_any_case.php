<?php

  $names = [ 'user_name', 'user-name', 'userName', 'UserName', 'user name', 'USER_NAME', 'HTMLParser',
             'XMLHttpRequest', 'address2Line', 'created_at', 'Crème_brûlée', "it's done", '', '0' ];

  $cases = [];

  foreach ( $names as $name )
    $cases [] = [ $name, padStrCamel ( $name ), padStrStudly ( $name ), padStrSnake ( $name ),
                  padStrKebab ( $name ), padStrHeadline ( $name ), padStrSnake ( $name, '.' ) ];

  $r = json_encode ( $cases, JSON_UNESCAPED_UNICODE );

  $t = json_encode ( [ padStrTitle ( 'hello wORLD' ), padStrTitle ( 'élan vital' ), padStrTitle ( 'user_name' ),
                       padStrTitle ( NULL ), padStrCamel ( NULL ), padStrSnake ( 'a b', '' ) ], JSON_UNESCAPED_UNICODE );

?>
