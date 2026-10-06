<?php

  // The harvest fetches every page from the name the request's Host header gives - a page
  // on another site that has its own name resolve to 127.0.0.1 (DNS rebinding) would have
  // its own server crawled and stored as the examples.

  $curl   = padCurl ( [ 'url' => $padHost . 'examples/?build', 'headers' => [ 'Host' => 'rebound.example' ] ] );
  $answer = $curl ['result'] . ': ' . trim ( $curl ['data'] );

?>
