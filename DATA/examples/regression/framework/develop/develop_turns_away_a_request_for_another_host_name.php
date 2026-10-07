<?php

  // A page on another site that has its own name resolve to 127.0.0.1 reaches the loopback
  // address too (DNS rebinding) - but its requests name that site in their Host header.

  $curl   = padCurl ( [ 'url' => $padHost . 'develop/?coverage', 'headers' => [ 'Host' => 'rebound.example' ] ] );
  $answer = $curl ['result'] . ': ' . trim ( $curl ['data'] );

?>
