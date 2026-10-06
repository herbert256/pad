<?php

  // A page holding the request's CSP nonce is never stored: a later hit's header would
  // name a nonce of its own, and the script in the stored copy would be refused. So the
  // script page is built fresh for each fetch, and its nonces differ - it was served from
  // the cache, the first visitor's nonce to everyone.

  $url = $padHost . 'regression/cache_file/?script&padInclude&nonce';

  $one = padCurl ( $url );
  $two = padCurl ( $url );

  $nonceResult = 'built fresh: ' . ( $one ['data'] !== $two ['data'] ? 'yes' : 'NO' );

?>
