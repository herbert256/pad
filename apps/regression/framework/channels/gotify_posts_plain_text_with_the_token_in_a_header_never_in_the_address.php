<?php

  // Gotify, answered by a fake: the message goes to the server's /message - the server of the
  // configuration, a trailing slash or not - as JSON with the title, the plain text and the
  // priority asked for, marked text/plain so a client renders no markdown a value holds (an
  // image would be fetched), the url as the address a click opens. The token travels as a
  // bearer token in the Authorization header, never in the address. A 401 is the reason the
  // server gave in its errorDescription.

  $_ENV ['GOTIFY_URL']   = 'https://push.example.de/';
  $_ENV ['GOTIFY_TOKEN'] = 'AWH0wZ5r0Mbac.r';

  padCurlFake ( [ 'https://push.example.de/message' => function ( $request ) {
    return str_ends_with ( $request ['headers'] ['Authorization'] ?? '', 'AWH0wZ5r0Mbac.r' )
         ? [ 'status' => 200, 'data' => [ 'id' => 25, 'appid' => 5, 'message' => '...', 'date' => '2026-11-01T12:00:00Z' ] ]
         : [ 'status' => 401, 'data' => [ 'error' => 'Unauthorized', 'errorCode' => 401,
                                          'errorDescription' => 'you need to provide a valid access token or user credentials to access this api' ] ];
  } ] );

  $calls = function () {
    $made = [];
    foreach ( padCurlRecorded () as $call )
      $made [] = [ 'call'    => $call ['method'] . ' ' . $call ['url'],
                   'headers' => array_intersect_key ( $call ['headers'], array_flip ( [ 'Content-Type', 'Authorization', 'X-Gotify-Key' ] ) ),
                   'body'    => json_decode ( (string) $call ['post'], TRUE ) ];
    padCurlFake ( [] );
    return $made;
  };

  $results = fn () => array_map ( fn ( $one ) => $one ['ok'] ? 'sent' : $one ['error'], padNotifyResults () );

  padNotify ( [ 'id' => 7 ], 'long', [ 'text' => 'Order 42 of Ann ![x](https://tracker.example/p.png) <b>has shipped</b>', 'title' => 'Order 42 shipped',
                                       'url' => 'https://shop.example/orders/42' ], [ 'gotify' ], [ 'gotify' => [ 'priority' => 8 ] ] );

  $report ['sent'] = [ 'calls' => $calls (), 'results' => $results () ];

  padNotify ( [ 'id' => 8 ], 'shipped', [ 'order' => 43, 'items' => 1, 'name' => 'Bo', 'url' => '' ], [ 'gotify' ],
              [ 'gotify' => [ 'token' => 'AnotherOne12345' ] ] );

  $call = $calls () [0];

  $report ['refused'] = [ 'extras' => $call ['body'] ['extras'], 'priority' => $call ['body'] ['priority'] ?? 'the application\'s', 'results' => $results () ];

  $json = json_encode ( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

?>
