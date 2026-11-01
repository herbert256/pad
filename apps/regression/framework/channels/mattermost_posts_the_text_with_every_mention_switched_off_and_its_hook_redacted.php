<?php

  // Mattermost's incoming webhook, answered by a fake: the text goes as JSON to the hook of
  // the configuration. Mattermost notifies on @channel, @all, @here and @name in it, read as
  // its parser reads them - a word after a run of - . _ : too, the part of a word after a
  // . - or : (x.@channel), an @ its markdown makes of &#64; or &commat; - so each @ no letter
  // or digit stands before gets a zero-width space after it (shown here as <zwsp>): the text
  // reads the same and pings nobody, an e-mail address stays as it is, and a run of @ is
  // split before its name alone, as the word of one is no mention. The hook's id is its
  // credential: the address _events/curl.php hears has it redacted. A 400 is the message of
  // Mattermost's JSON.

  $_ENV ['MATTERMOST_WEBHOOK_URL'] = 'https://chat.example.com/hooks/xi3k8ub7ofgc7m9jtcwa8b5z1a';

  $GLOBALS ['heardCurlUrls'] = [];

  padCurlFake ( [ 'https://chat.example.com/hooks/*' => function ( $request ) {
    return str_contains ( (string) $request ['post'], 'broken' )
         ? [ 'status' => 400, 'data' => [ 'id' => 'web.incoming_webhook.parse.app_error', 'message' => 'Unable to parse incoming data',
                                          'detailed_error' => '', 'request_id' => 'ru7mh6eo4tfxpb4t3xjdqexgkr', 'status_code' => 400 ] ]
         : [ 'status' => 200, 'data' => 'ok', 'headers' => [ 'Content-Type' => 'text/plain' ] ];
  } ] );

  $results = fn () => array_map ( fn ( $one ) => $one ['ok'] ? 'sent' : $one ['error'], padNotifyResults () );

  padNotify ( [ 'id' => 7 ], 'long', [ 'text' => "@channel order 42 for @ann (ann@example.com) shipped\n-@all _@here :@bo, and @@cy\n"
                                         . 'x.@channel end-@all a:@here Bob.@channel @ann.@channel &#64;all &commat;here &#x40;channel @&#99;hannel \\@all', 'title' => 'Order 42' ],
              [ 'mattermost' ] );

  $call = padCurlRecorded () [0];

  $report ['sent'] = [ 'call'    => $call ['method'] . ' ' . $call ['url'],
                       'type'    => $call ['headers'] ['Content-Type'] ?? '',
                       'text'    => str_replace ( "\u{200B}", '<zwsp>', json_decode ( $call ['post'], TRUE ) ['text'] ),
                       'heard'   => $GLOBALS ['heardCurlUrls'],
                       'results' => $results () ];

  padNotify ( [ 'id' => 8 ], 'long', [ 'text' => 'broken', 'title' => 'x' ], [ 'mattermost' ] );

  $report ['refused'] = $results ();

  // The references a section can hand the channel as they stand - its markdown reads each
  // as an @ - straight to its send.

  ( padNotifyKind ( 'mattermost' ) ['send'] ) ( [ 'text' => '&#64;all &#x40;channel &#00064;here @&#99;hannel &commat;&commat;x mail&#64;example.com' ],
                                                [ 'url' => 'https://chat.example.com/hooks/xi3k8ub7ofgc7m9jtcwa8b5z1a' ] );

  $calls = padCurlRecorded ();

  $report ['references'] = str_replace ( "\u{200B}", '<zwsp>', json_decode ( end ( $calls ) ['post'], TRUE ) ['text'] );

  $json = json_encode ( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

?>
