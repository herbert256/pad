<?php

  // The fetches of this request answered from fakes: padCurl, padCurlCached (which passes
  // its cache by while faking), padPrefetch, and in the template {curl} and data=. The
  // URLs fetched are read back after the template made its own.

  padCurlFake ( [
    'https://api.example.com/rates'  => [ 'data' => [ 'eur' => 1.08 ] ],
    'https://api.example.com/list*'  => [ 'status'  => 200, 'data' => "name,price\nTea,2\nCake,3",
                                          'headers' => [ 'Content-Type' => 'text/csv', 'X-Fake' => 'yes' ] ],
    'api.example.com/down'           => 503,
    'https://api.example.com/echo'   => fn ( $request ) => $request ['method'] . ' ' . $request ['post']
  ] );

  $rates  = padCurl ( 'https://api.example.com/rates' );
  $cached = padCurlCached ( 'https://api.example.com/rates', 600 );
  $down   = padCurl ( 'https://api.example.com/down' );
  $echo   = padCurl ( [ 'url' => 'https://api.example.com/echo', 'post' => 'a=1' ] );

  padPrefetch ( [ 'menu' => 'https://api.example.com/list?for=menu' ] );

  $summary = json_encode ( [
    'rates'  => [ $rates ['result'], $rates ['type'], $rates ['data'] ],
    'cached' => [ $cached ['result'], $cached ['cache'] ],
    'down'   => [ $down ['result'], $down ['data'] ],
    'echo'   => $echo ['data'],
  ], JSON_UNESCAPED_SLASHES );

  function httpFakeCalls () {
    return implode ( ' | ', array_map ( fn ( $call ) => $call ['method'] . ' ' . $call ['url'], padCurlRecorded () ) );
  }

?>
