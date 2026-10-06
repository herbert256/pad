<?php

  // The cache holds what an anonymous visitor gets and serves it only to one. A request
  // carrying a cookie of its own - a login, a session - is built fresh: the URI alone used
  // to key the cache, so the first visitor's page went to everyone. The probe's body is its
  // build moment, so equal bodies are a hit.

  $url = $padHost . 'regression/cache_file/?probe&padInclude&identity';

  $anonOne = padCurl ( $url );
  $alice   = padCurl ( [ 'url' => $url, 'cookies' => [ 'viewer' => 'Alice' ] ] );
  $anonTwo = padCurl ( $url );

  $identityResult = 'anonymous cached: ' . ( $anonOne ['data'] === $anonTwo ['data'] ? 'yes' : 'NO' )
                  . ', cookie fresh: '   . ( $alice   ['data'] !== $anonOne ['data'] ? 'yes' : 'NO' );

?>
