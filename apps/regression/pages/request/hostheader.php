<?php

  // The engine fetches its own pages - {get}, {curl 'SELF://...'}, {page} over HTTP - from
  // this server, whatever Host the request named: the address was built from the visitor's
  // Host header, and a request saying Host: 127.0.0.1:1 - or attacker.example - made the
  // server fetch from there and splice what came back into the page. request/hostfetch is
  // asked for here with a Host naming a machine that does not exist; its two fetches still
  // reach this server, under that Host. (A port in the Host is held by the framework case
  // build/a_self_fetch_goes_to_a_port_the_host_header_cannot_choose: asked here it would be
  // php -S's own port, which that server keeps.)

  $hhAnswer = padCurl ( [ 'url' => $padGoExt . 'request/hostfetch&padInclude', 'headers' => [ 'Host' => 'attacker.invalid' ] ] );

  $hhResult = $hhAnswer ['result'] . ' ' . trim ( $hhAnswer ['data'] );

?>
