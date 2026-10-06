<?php

  // With no $padMailFrom the sender is noreply@ the host the site is served on - an IP
  // literal in brackets, which for an IPv6 address is [IPv6:...] as an address must write
  // it: a site on http://[::1]/ made noreply@[::1], which is no mail address, and every
  // mail it sent stopped on that error.

  $fromSaved = $padHost;
  $fromShown = [];

  foreach ( [ 'http://[::1]:8080/pad/', 'http://127.0.0.1/pad/', 'http://localhost/pad/', 'https://shop.example.com/' ] as $fromHost ) {

    $padHost = $fromHost;

    padMail ( 'ann@example.com', '', 'Hello', [], [ 'html' => '<p>Hello</p>' ] );

    $fromShown [] = $padMailLast ['from'];

    unlink ( $padMailLast ['file'] );

  }

  $padHost = $fromSaved;

  echo implode ( ' | ', $fromShown );

?>
