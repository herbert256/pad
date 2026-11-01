<?php

  // Mattermost's incoming webhook, answered by a fake: the text goes as JSON to the hook of
  // the configuration. Mattermost notifies on @channel, @all, @here and @name in it, so each
  // @ that starts a word as the server reads them - also after a run of - . _ : - gets a
  // zero-width space after it (shown here as <zwsp>): the text reads the same and pings
  // nobody, an e-mail address stays as it is. The hook's id is its credential: the address
  // _events/curl.php hears has it redacted. A 400 is the message of Mattermost's JSON.

  $_ENV ['MATTERMOST_WEBHOOK_URL'] = 'https://chat.example.com/hooks/xi3k8ub7ofgc7m9jtcwa8b5z1a';

  $GLOBALS ['heardCurlUrls'] = [];

  padCurlFake ( [ 'https://chat.example.com/hooks/*' => function ( $request ) {
    return str_contains ( (string) $request ['post'], 'broken' )
         ? [ 'status' => 400, 'data' => [ 'id' => 'web.incoming_webhook.parse.app_error', 'message' => 'Unable to parse incoming data',
                                          'detailed_error' => '', 'request_id' => 'ru7mh6eo4tfxpb4t3xjdqexgkr', 'status_code' => 400 ] ]
         : [ 'status' => 200, 'data' => 'ok', 'headers' => [ 'Content-Type' => 'text/plain' ] ];
  } ] );

  $results = fn () => array_map ( fn ( $one ) => $one ['ok'] ? 'sent' : $one ['error'], padNotifyResults () );

  padNotify ( [ 'id' => 7 ], 'long', [ 'text' => "@channel order 42 for @ann (ann@example.com) shipped\n-@all _@here :@bo, and @@cy", 'title' => 'Order 42' ],
              [ 'mattermost' ] );

  $call = padCurlRecorded () [0];

  $report ['sent'] = [ 'call'    => $call ['method'] . ' ' . $call ['url'],
                       'type'    => $call ['headers'] ['Content-Type'] ?? '',
                       'text'    => str_replace ( "\u{200B}", '<zwsp>', json_decode ( $call ['post'], TRUE ) ['text'] ),
                       'heard'   => $GLOBALS ['heardCurlUrls'],
                       'results' => $results () ];

  padNotify ( [ 'id' => 8 ], 'long', [ 'text' => 'broken', 'title' => 'x' ], [ 'mattermost' ] );

  $report ['refused'] = $results ();

  $json = json_encode ( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

?>
