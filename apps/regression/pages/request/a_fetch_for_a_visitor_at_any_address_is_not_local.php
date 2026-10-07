<?php

  // A fetch made for a visitor from elsewhere said so only when its address named this
  // machine in a form the comparison knew: 0.0.0.0, ::ffff:7f00:1, an expanded ::1, a name
  // that resolves to ::1 alone, a site that redirects here - all arrived from loopback with
  // nothing forwarded and were taken for local. Every fetch made for such a request now says
  // so, whatever its address.

  $shown = [];

  foreach ( [ 'zero', 'mapped' ] as $form ) {
    $curl     = padCurl ( [ 'url'     => $padHost . "regression/pages/?request/fetching_debugged_inner_at_$form&padInclude",
                            'headers' => [ 'X-Forwarded-For' => '203.0.113.9' ] ] );
    $shown [] = "$form: " . ( str_contains ( $curl ['data'], 'pad-debug' ) ? 'YES' : 'no' );
  }

  $shown = implode ( ', ', $shown );

?>
