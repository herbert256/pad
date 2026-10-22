<?php

  // padSesID and padReqID are kept on the directory of the application's entry point - one
  // pair per browser and application - whether a page is asked by a clean URL below it or
  // by ?page: they had no path, so the browser kept them on the directory of each address,
  // and under clean URLs /products/42 and /docs/a/b each got a pair of ten-year cookies of
  // their own, a session id per directory and a parent request from that directory only.

  $ckDir    = parse_url ( $padHost, PHP_URL_PATH ) . 'regression/clean_urls/';
  $ckResult = '';

  foreach ( [ 'clean' => 'regression/clean_urls/index.php/products/42', 'query' => 'regression/clean_urls/?index' ] as $ckHow => $ckAsk ) {

    $ckGot  = padCurl ( $padHost . $ckAsk );
    $ckPath = $ckGot ['cookieAttributes'] ['padReqID'] ['path'] ?? 'none';

    $ckResult .= "$ckHow: " . substr ( $ckGot ['result'], 0, 3 ) . ' ' . ( $ckPath === $ckDir ? "the application's directory" : $ckPath ) . ' | ';

  }

?>
