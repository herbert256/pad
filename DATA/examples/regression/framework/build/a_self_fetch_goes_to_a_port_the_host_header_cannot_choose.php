<?php

  // Where the engine's own fetches connect (padSelfConnect, lib/paths.php) when the server
  // is not php -S: Apache with UseCanonicalName Off, its default, fills SERVER_PORT from the
  // port a Host header names, so Host: x:22 sent the fetch to port 22 of this machine. A
  // port in the Host header counts when it is the scheme's own - 80, 443 - or $padHosts lists
  // the host with it; any other goes to the scheme's port. A Host without a port leaves the
  // server's port as it is, and php -S, whose SERVER_PORT is the port it listens on, keeps it.

  $connectServer = $_SERVER;
  $connectHost   = $padHost;
  $connectHosts  = $padHosts;
  $connectBase   = $padHostBase;

  $padHostBase = '';
  $padHosts    = [];

  function connectTry ( $sapi, $host, $port, $scheme = 'http' ) {

    global $padHost;

    $_SERVER ['HTTP_HOST']   = $host;
    $_SERVER ['SERVER_PORT'] = $port;
    $_SERVER ['SERVER_ADDR'] = '127.0.0.1';

    $padHost = "$scheme://" . ( str_contains ( $host, ':' ) ? $host : ( in_array ( $port, [ '80', '443' ] ) ? $host : "$host:$port" ) ) . '/pad/';

    return implode ( ',', padSelfConnect ( $sapi ) );

  }

  $connectResult = implode ( ' | ', [
    connectTry ( 'apache2handler', 'x:22',           '22'   ),
    connectTry ( 'apache2handler', 'localhost',      '80'   ),
    connectTry ( 'apache2handler', 'localhost:80',   '80'   ),
    connectTry ( 'apache2handler', 'example.com',    '8080' ),
    connectTry ( 'apache2handler', 'x:22',           '22', 'https' ),
    connectTry ( 'cli-server',     '127.0.0.1:8805', '8805' )
  ] );

  $padHosts = [ 'localhost:8080' ];

  $connectResult .= ' | ' . connectTry ( 'apache2handler', 'localhost:8080', '8080' );

  $_SERVER     = $connectServer;
  $padHost     = $connectHost;
  $padHosts    = $connectHosts;
  $padHostBase = $connectBase;

?>
