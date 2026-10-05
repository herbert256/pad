<?php

  // Content negotiation: an Accept header that prefers JSON or CSV gets the data, a browser's
  // gets the page, and a page that exposes nothing renders its HTML whatever is asked - as
  // does a page that only embeds one that does. An exposing page says Vary: Accept.

  function formatAccept ( $page, $accept ) {

    global $padGoExt;

    $r = padCurl ( [ 'url' => $padGoExt . "$page&padInclude", 'headers' => [ 'Accept' => $accept ] ] );

    return explode ( ';', $r ['headers'] ['Content-Type'] ?? '' ) [0] . ' ' . ( $r ['headers'] ['Vary'] ?? '-' );

  }

  echo formatAccept ( 'format/orders', 'application/json' ), ' / ',
       formatAccept ( 'format/orders', 'text/csv' ), ' / ',
       formatAccept ( 'format/orders', 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8' ), ' / ',
       formatAccept ( 'format/orders', 'text/html;q=0.5, application/json' ), ' / ',
       formatAccept ( 'format/orders', '*/*' ), ' / ',
       formatAccept ( 'format/host',   'application/json' );

?>
