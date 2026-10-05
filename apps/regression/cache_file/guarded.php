<?php

  // A page under a directory guard stays out of the page cache: a hit is answered before
  // any guard runs, so a cached copy would go to whoever asked, whatever the guard says. The
  // guard here wants a header, and a request with it gets a fresh build each time while one
  // without it is refused - it was given the copy the first request left in the cache.

  $url = $padHost . 'regression/cache_file/?locked/probe&padInclude&guarded';

  $keyOne = padCurl ( [ 'url' => $url, 'headers' => [ 'X-Key' => 'open' ] ] );
  $keyTwo = padCurl ( [ 'url' => $url, 'headers' => [ 'X-Key' => 'open' ] ] );
  $noKey  = padCurl ( $url );

  $guardedResult = 'built fresh: ' . ( $keyOne ['data'] !== $keyTwo ['data'] ? 'yes' : 'NO' )
                 . ', without the key: ' . $noKey ['result'];

?>
