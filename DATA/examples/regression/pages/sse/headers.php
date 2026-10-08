<?php

  // What a stream says about itself: the event-stream type, never stored by a cache, never
  // buffered by a proxy - and the id the browser sends back on a reconnect, as a header or
  // in the query, is the producer's to read, with a line break that would end the field
  // taken out.

  $curl = padCurl ( [ 'url'     => $padHost . 'regression/pages/?sse/lastid',
                      'headers' => [ 'Last-Event-ID' => '41' ] ] );

  $query = padCurl ( $padHost . 'regression/pages/?sse/lastid&lastEventId=' . rawurlencode ( "42\nevent: x" ) );

  $headers = array_change_key_case ( $curl ['headers'], CASE_LOWER );

  echo 'type: '      . ( $headers ['content-type']      ?? '' ) . "\n"
     . 'cache: '     . ( $headers ['cache-control']     ?? '' ) . "\n"
     . 'buffering: ' . ( $headers ['x-accel-buffering'] ?? '' ) . "\n"
     . 'header: '    . trim ( $curl  ['data'] ) . "\n"
     . 'query: '     . trim ( $query ['data'] ) . "\n";

?>
