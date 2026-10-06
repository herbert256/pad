<?php

  // The engine fetches its own pages - {get}, {curl 'SELF://...'}, {page} over HTTP - from
  // this server, whatever Host the request named: the address was built from the visitor's
  // Host header, and a request saying Host: 127.0.0.1:1 - or attacker.example - made the
  // server fetch from there and splice what came back into the page. request/hostfetch is
  // asked for here with a Host naming a machine that does not exist; its two fetches still
  // reach this server, under that Host. (Not a port: under Apache's UseCanonicalName Off,
  // SERVER_PORT is the port the Host header names - see padSelfConnect.)

  $hhAnswer = padCurl ( [ 'url' => $padGoExt . 'request/hostfetch&padInclude', 'headers' => [ 'Host' => 'attacker.invalid' ] ] );

  $hhResult = $hhAnswer ['result'] . ' ' . trim ( $hhAnswer ['data'] );

?>
