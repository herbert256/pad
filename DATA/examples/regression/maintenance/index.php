<?php

  // Takes this application down - as pad down does, with a secret - and fetches it the
  // ways maintenance promises to answer: a page 503 with a Retry-After and the 503 page of
  // _errors/, the plain message without error pages, a page that is not there 503 as well,
  // the secret a redirect to the front page with the cookie, and that cookie let through.
  // Then up again, and the page is there as before. The request that does this is past the
  // gate already; the suite fetches this application's pages one at a time.

  $secret = 'regression-secret';
  $base   = $padHost . 'regression/maintenance/';
  $probes = [];

  padMaintenanceDown ( $padApp, $secret, 30, 'Back in half a minute' );

  try {

    $page   = padCurl ( $base . '?about' );
    $plain  = padCurl ( $base . '?about&plain' );
    $gone   = padCurl ( $base . '?nothere' );
    $bypass = padCurl ( [ 'url' => $base . "?$secret", 'options' => [ 'FOLLOWLOCATION' => FALSE ] ] );
    $wrong  = padCurl ( $base . '?wrong-secret' );

    $cookie = $bypass ['cookies'] [ padMaintenanceCookie () ] ?? '';
    $inside = padCurl ( [ 'url' => $base . '?about&padInclude', 'cookies' => [ padMaintenanceCookie () => $cookie ] ] );

  } finally {

    padMaintenanceUp ( $padApp );

  }

  $after = padCurl ( $base . '?about&padInclude' );

  foreach ( compact ( 'page', 'plain', 'gone', 'bypass', 'wrong', 'inside', 'after' ) as $name => $curl )
    $probes [] = [
      'name'     => $name,
      'status'   => $curl ['result'],
      'retry'    => $curl ['headers'] ['Retry-After']   ?? '-',
      'cache'    => $curl ['headers'] ['Cache-Control'] ?? '-',
      'location' => str_replace ( $padHost, 'HOST/', $curl ['headers'] ['Location'] ?? '-' ),
      'body'     => trim ( $curl ['data'] )
    ];

  $cookieSet = ( $cookie !== '' ) ? 'yes' : 'no';
  $stillDown = padMaintenanceRead ( $padApp ) ? 'yes' : 'no';

?>
