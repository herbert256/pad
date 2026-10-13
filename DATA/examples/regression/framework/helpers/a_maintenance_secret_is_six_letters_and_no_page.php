<?php

  // The secret of maintenance mode is asked as ?<secret>: the name of a page every visitor
  // asks gave all of them the cookie, and one no address can ask locked its developer out.

  $secrets = [];

  foreach ( [ 'letmein', 'guestbook', 'todo', 'index', 'a', 'a b c d e', 'x&y12345', str_repeat ( 'k', 65 ) ] as $one )
    $secrets [] = [ 'secret' => $one, 'valid' => padMaintenanceSecretValid ( 'demo', $one ) ? 'yes' : 'no' ];

?>
