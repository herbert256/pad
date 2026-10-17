<?php

  // Only this machine makes the first user (login.php), with the editor opened up as well:
  // $editRemote on and no $editHosts lets any Host past the guard, and a page on another site
  // whose name rebinds to 127.0.0.1 posts from the developer's own browser - the loopback
  // address, nothing forwarded, but its own name in the Host header.

  $keep = $_SERVER;

  $GLOBALS ['editRemote'] = TRUE;
  $GLOBALS ['editHosts']  = [];

  $_SERVER ['REMOTE_ADDR'] = '127.0.0.1';

  foreach ( [ 'HTTP_X_FORWARDED_FOR', 'HTTP_FORWARDED', 'HTTP_X_REAL_IP', 'HTTP_VIA', 'HTTP_X_PAD_REMOTE' ] as $header )
    unset ( $_SERVER [$header] );

  $hosts = [];

  foreach ( [ 'localhost:8080', '127.0.0.1', '[::1]', 'LocalHost', 'rebind.attacker.example', 'localhost.attacker.example' ] as $host ) {
    $_SERVER ['HTTP_HOST'] = $host;
    $hosts [] = [ 'host' => $host, 'guard' => editAllowed () ? 'in' : 'out', 'first' => editThisMachine () ? 'may' : 'may not' ];
  }

  $_SERVER ['HTTP_HOST']            = 'localhost';
  $_SERVER ['HTTP_X_FORWARDED_FOR'] = '203.0.113.9';

  $forwarded = editThisMachine () ? 'may' : 'may not';

  unset ( $_SERVER ['HTTP_X_FORWARDED_FOR'] );

  $_SERVER ['REMOTE_ADDR'] = '192.0.2.7';

  $other = editThisMachine () ? 'may' : 'may not';

  $_SERVER = $keep;

  unset ( $GLOBALS ['editRemote'], $GLOBALS ['editHosts'] );

?>
