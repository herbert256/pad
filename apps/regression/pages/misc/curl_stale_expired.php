<?php

  // A copy of an hour ago, kept for a source on a port nothing listens on, with a ttl of a
  // minute and $padCurlStale ten minutes beyond it: past both the fetch fails and there is
  // no copy to serve - the failure is the page's, as the other stores have it once their
  // entry expired.

  $expiredUrl   = 'http://127.0.0.1:1/expired.json';
  $padCurlStale = 600;

  padFilePut ( 'cache/curl/' . padCurlCacheKey ( [ 'url' => $expiredUrl ] ),
               ( time () - 3600 ) . "\n" . json_encode ( [ 'url' => $expiredUrl, 'result' => 200, 'type' => 'json', 'data' => '{"rate":1.5}' ] ) );

?>
