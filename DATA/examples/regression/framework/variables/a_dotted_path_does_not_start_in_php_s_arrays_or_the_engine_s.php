<?php

  // A secret where SetEnv and PHP-FPM's env[...] put one, the input of the engine's last
  // fetch with its password, and the message TAGS.md hands to templates as $padMailLast.

  $_SERVER ['DB_PASSWORD'] = 's3cret';
  $_ENV    ['DB_PASSWORD'] = 's3cret';

  $GLOBALS ['padCurlLast'] = [ 'input' => [ 'password' => 'apipw' ] ];
  $GLOBALS ['padMailLast'] = [ 'subject' => 'Hi' ];

  $u = [ 'name' => 'Ann' ];

?>
