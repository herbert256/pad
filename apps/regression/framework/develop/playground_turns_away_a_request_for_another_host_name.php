<?php

  // A page on another site that has its own name resolve to 127.0.0.1 (DNS rebinding)
  // reaches the loopback address too - local by its address, and as a page of the same
  // site it can read the form and its CSRF token - but its requests name that site in
  // their Host header.

  $curl   = padCurl ( [ 'url' => $padHost . 'playground/', 'headers' => [ 'Host' => 'rebound.example' ] ] );
  $answer = $curl ['result'] . ': ' . trim ( $curl ['data'] );

?>
