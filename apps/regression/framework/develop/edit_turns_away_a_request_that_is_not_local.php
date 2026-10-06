<?php

  // The editor writes PHP files, so it answers this machine only: a request forwarded for
  // another address is refused by its guard - and, not being local, learns nothing more.

  $curl   = padCurl ( [ 'url' => $padHost . 'edit/?login', 'headers' => [ 'X-Forwarded-For' => '203.0.113.9' ] ] );
  $answer = $curl ['result'] . ': ' . trim ( $curl ['data'] );

?>
