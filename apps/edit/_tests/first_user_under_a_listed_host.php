<?php

  // A name listed in $editHosts is this machine's as much as localhost: from the loopback
  // address, nothing forwarded, a request naming pad.test - a local development name the
  // owner listed - may make the first user (login.php), where it was let past the guard and
  // then refused with "The first user can only be made from this machine.". A name nobody
  // listed is still refused, and opened up with no list, which lets any Host past the guard,
  // only this machine's own names may make the first user.

  $keep = $_SERVER;

  $_SERVER ['REMOTE_ADDR'] = '127.0.0.1';

  foreach ( [ 'HTTP_X_FORWARDED_FOR', 'HTTP_FORWARDED', 'HTTP_X_REAL_IP', 'HTTP_VIA', 'HTTP_X_PAD_REMOTE' ] as $header )
    unset ( $_SERVER [$header] );

  $hosts = [];

  foreach ( [ [ FALSE, [ 'pad.test' ] ], [ TRUE, [ 'pad.test' ] ], [ TRUE, [] ] ] as [ $remote, $listed ] ) {

    $GLOBALS ['editRemote'] = $remote;
    $GLOBALS ['editHosts']  = $listed;

    foreach ( [ 'pad.test', 'PAD.test:8080', 'localhost', 'rebind.attacker.example' ] as $host ) {
      $_SERVER ['HTTP_HOST'] = $host;
      $hosts [] = [ 'remote' => $remote ? 'on' : 'off', 'listed' => implode ( ',', $listed ) ?: 'none', 'host' => $host,
                    'first' => editThisMachine () ? 'may' : 'may not' ];
    }

  }

  $_SERVER = $keep;

  unset ( $GLOBALS ['editRemote'], $GLOBALS ['editHosts'] );

?>
