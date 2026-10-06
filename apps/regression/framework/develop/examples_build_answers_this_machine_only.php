<?php

  // The harvest empties DATA/examples and fills it from every page, fetched from the name in
  // the request's Host header, and ?show serves what it stored as it is: a visitor - here a
  // request forwarded for another address - is turned away before it starts. Asked without
  // its go, so that on a server where it is not turned away it still does nothing.

  $curl   = padCurl ( [ 'url' => $padHost . 'examples/?build', 'headers' => [ 'X-Forwarded-For' => '203.0.113.9' ] ] );
  $answer = $curl ['result'] . ': ' . trim ( $curl ['data'] );

?>
