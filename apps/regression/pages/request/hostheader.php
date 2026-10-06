<?php

  // The engine fetches its own pages - {get}, {curl 'SELF://...'}, {page} over HTTP - from
  // this server, whatever Host the request named: the address was built from the visitor's
  // Host header, and a request saying Host: 127.0.0.1:1 - or attacker.example - made the
  // server fetch from there and splice what came back into the page. request/hostfetch is
  // asked for here with a Host whose port nothing listens on; its two fetches still reach
  // this server, under that Host.

  $hhAnswer = padCurl ( [ 'url' => $padGoExt . 'request/hostfetch&padInclude', 'headers' => [ 'Host' => '127.0.0.1:1' ] ] );

  $hhResult = $hhAnswer ['result'] . ' ' . trim ( $hhAnswer ['data'] );

?>
