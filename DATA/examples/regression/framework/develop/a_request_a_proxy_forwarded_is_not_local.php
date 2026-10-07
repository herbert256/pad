<?php

  // A proxy on the same machine connects from loopback for every visitor; the headers it
  // adds are how a request says it was forwarded. X-Forwarded-For and Forwarded were read,
  // but a proxy that sends only the scheme, a Via line or its CDN's client address - an
  // nginx with proxy_set_header X-Forwarded-Proto alone, a CDN with a header of its own - had every
  // visitor taken for this machine, with the full error report and the debug tools.

  $seen = [];

  foreach ( [ 'X-Forwarded-Proto' => 'https', 'Via' => '1.1 proxy', 'CF-Connecting-IP' => '203.0.113.9',
              'True-Client-IP' => '203.0.113.9', 'X-Forwarded-Server' => 'edge' ] as $header => $value ) {

    $curl   = padCurl ( [ 'url' => $padHost . 'regression/framework/?develop/local_as_the_engine_sees_it&padInclude',
                          'headers' => [ $header => $value ] ] );

    $seen [] = "$header: " . trim ( $curl ['data'] );

  }

  $seen = implode ( ', ', $seen );

?>
