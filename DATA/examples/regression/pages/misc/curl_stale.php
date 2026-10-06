<?php

  // A copy of an hour ago, kept for a source on a port nothing listens on: past its ttl it
  // is fetched again, the fetch fails, and the copy is what the page gets.

  $staleUrl = 'http://127.0.0.1:1/rates.json';

  padFilePut ( 'cache/curl/' . padCurlCacheKey ( [ 'url' => $staleUrl ] ),
               ( time () - 3600 ) . "\n" . json_encode ( [ 'url' => $staleUrl, 'result' => 200, 'type' => 'json', 'data' => '{"rate":1.5}' ] ) );

?>
