<?php

  // The regression runner acts on a plain GET - record writes an answer into a store under
  // apps/, Test runs suites and wipes DATA/dumps, Build wipes the results first - so a
  // request forwarded for another address is refused by its guard before anything runs.
  // The overview itself stays open; it only reads.

  $answer = '';

  foreach ( [ 'record&suite=other&name=demo/index', 'errors/index&test', 'index' ] as $ask ) {
    $curl    = padCurl ( [ 'url' => $padHost . "regression/main/?$ask", 'headers' => [ 'X-Forwarded-For' => '203.0.113.9' ] ] );
    $answer .= $curl ['result'] . ' ';
  }

?>
