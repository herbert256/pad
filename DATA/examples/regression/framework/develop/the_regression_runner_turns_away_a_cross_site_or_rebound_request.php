<?php

  // An <img src="http://localhost/pad/regression/main/?index&test"> on a page of another
  // site comes from this machine's own browser - loopback, nothing forwarded - but the
  // browser says where it was sent from; a site whose own name resolves to 127.0.0.1 (DNS
  // rebinding) names that site in its Host header. Both are refused, as develop refuses
  // them. Asked is record of a suite that does not exist: a 500 would mean it ran.

  $answer = '';

  foreach ( [ [ 'Sec-Fetch-Site' => 'cross-site' ], [ 'Sec-Fetch-Site' => 'same-site' ], [ 'Host' => 'rebound.example' ] ] as $headers ) {
    $curl    = padCurl ( [ 'url' => $padHost . 'regression/main/?record&suite=nosuch&name=x', 'headers' => $headers ] );
    $answer .= $curl ['result'] . ' ';
  }

?>
