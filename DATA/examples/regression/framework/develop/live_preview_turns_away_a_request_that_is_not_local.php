<?php

  $curl   = padCurl ( [ 'url' => $padHost . 'live/', 'headers' => [ 'X-Forwarded-For' => '203.0.113.9' ] ] );
  $answer = $curl ['result'] . ': ' . trim ( $curl ['data'] );

?>
