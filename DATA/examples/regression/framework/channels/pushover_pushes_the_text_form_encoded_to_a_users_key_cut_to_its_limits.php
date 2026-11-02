<?php

  // Pushover's Message API, answered by a fake: the application's token and the user's key -
  // the row's field pushover - go form-encoded with the title, the plain text as the message
  // and the url, the options priority and sound beside them. A text past 1024 characters is
  // cut there and a title past 250 there, a url past 512 is left out. A row whose field is no
  // user key is not sent to, and a 400 is the reason the API gave in its errors. The token
  // and key are the examples of Pushover's own documentation.

  $token = 'azGDORePK8gMaC0QOYAMyEEuzJnyUi';
  $ann   = [ 'id' => 7, 'pushover' => 'uQiRzpo4DXghDmr9QzzfQu27cmVRsG' ];

  padCurlFake ( [ 'https://api.pushover.net/1/messages.json' => function ( $request ) {
    parse_str ( (string) $request ['post'], $body );
    return ( $body ['user'] ?? '' ) === 'gznej3rKEVAvPUxu9vvNnqpmZpokzF'
         ? [ 'status' => 400, 'data' => [ 'user' => 'invalid', 'errors' => [ 'user identifier is invalid' ], 'status' => 0,
                                          'request' => '5042853c-402d-4a18-abcb-168734a801de' ] ]
         : [ 'status' => 200, 'data' => [ 'status' => 1, 'request' => '647d2300-702c-4b38-8b2f-d56326ae460b' ] ];
  } ] );

  $calls = function () {
    $made = [];
    foreach ( padCurlRecorded () as $call ) {
      parse_str ( (string) $call ['post'], $body );
      $made [] = [ 'call' => $call ['method'] . ' ' . $call ['url'], 'type' => $call ['headers'] ['Content-Type'] ?? '', 'body' => $body ];
    }
    padCurlFake ( [] );
    return $made;
  };

  $results = fn () => array_map ( fn ( $one ) => $one ['ok'] ? 'sent' : $one ['error'], padNotifyResults () );

  // 1. The text, the title and the url of the order, a high priority and a sound of its own.

  padNotify ( $ann, 'shipped', [ 'order' => 42, 'items' => 3, 'name' => 'Ann', 'url' => 'https://shop.example/orders/42' ], [ 'pushover' ],
              [ 'pushover' => [ 'token' => $token, 'priority' => 1, 'sound' => 'cosmic' ] ] );

  $report ['sent'] = [ 'calls' => $calls (), 'results' => $results () ];

  // 2. A text, a title and a url past what Pushover takes.

  padNotify ( $ann, 'long', [ 'text' => str_repeat ( 'é', 1100 ), 'title' => str_repeat ( 't', 300 ), 'url' => 'https://shop.example/' . str_repeat ( 'u', 500 ) ],
              [ 'pushover' ], [ 'pushover' => [ 'token' => $token ] ] );

  $body = $calls () [0] ['body'];

  $report ['cut'] = [ 'message' => mb_strlen ( $body ['message'] ), 'ends' => mb_substr ( $body ['message'], -4 ), 'title' => mb_strlen ( $body ['title'] ),
                      'url' => isset ( $body ['url'] ), 'results' => $results () ];

  // 3. A row whose field pushover is no user key, and a key Pushover does not know.

  padNotify ( [ 'id' => 8, 'pushover' => 'bo@example.com' ], 'shipped', [ 'order' => 43, 'items' => 1, 'name' => 'Bo', 'url' => '' ],
              [ 'pushover' ], [ 'pushover' => [ 'token' => $token ] ] );

  $report ['no key'] = [ 'calls' => count ( $calls () ), 'results' => $results () ];

  padNotify ( [ 'id' => 9, 'pushover' => 'gznej3rKEVAvPUxu9vvNnqpmZpokzF' ], 'shipped', [ 'order' => 44, 'items' => 1, 'name' => 'Cy', 'url' => '' ],
              [ 'pushover' ], [ 'pushover' => [ 'token' => $token ] ] );

  $report ['unknown'] = [ 'calls' => count ( $calls () ), 'results' => $results () ];

  $json = json_encode ( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

?>
