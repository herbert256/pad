<?php

  // The engine's own fetches - {get}, {curl 'SELF://...'}, {page} by HTTP - arrive from
  // loopback with nothing forwarded, so the page fetched for a visitor from elsewhere took
  // the request for this machine's own: the visitor got its {debug} boxes, its toolbar and,
  // with a tool's user agent, the JSON report of its errors. A page that gets a page with a
  // {debug} in it is asked as a visitor from elsewhere.

  $curl = padCurl ( [ 'url'     => $padHost . 'regression/pages/?request/getting_debugged_inner&padInclude',
                      'headers' => [ 'X-Forwarded-For' => '203.0.113.9' ] ] );

  $shown = str_contains ( $curl ['data'], 'pad-debug' ) ? 'YES' : 'no';

?>
