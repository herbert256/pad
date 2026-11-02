<?php

  // Twilio's Messages resource, answered by a fake: To, From and Body form-encoded to the
  // account's Messages.json, the account's SID and auth token as HTTP basic authentication -
  // or an API key and its secret, with a Messaging Service as the sender, which goes as
  // MessagingServiceSid. The recipient's phone loses its spaces and the like; one without +
  // and its country code is not sent to. A body past 1600 characters is cut there, and a 400
  // is the message of Twilio's JSON. The sender is the number of Twilio's own examples.

  // Deliberately synthetic, format-valid credentials for the fake transport.
  $twilioAccount = 'AC' . str_repeat ( '0', 32 );
  $twilioKey     = 'SK' . str_repeat ( '1', 32 );

  $_ENV ['TWILIO_ACCOUNT_SID'] = $twilioAccount;
  $_ENV ['TWILIO_AUTH_TOKEN']  = str_repeat ( 't', 32 );
  $_ENV ['TWILIO_FROM']        = '+15017122661';

  padCurlFake ( [ 'https://api.twilio.com/2010-04-01/Accounts/*' => function ( $request ) {
    parse_str ( (string) $request ['post'], $body );
    return ( $body ['To'] ?? '' ) === '+15005550001'
         ? [ 'status' => 400, 'data' => [ 'code' => 21211, 'message' => "Invalid 'To' Phone Number: +1500555XXXX",
                                          'more_info' => 'https://www.twilio.com/docs/errors/21211', 'status' => 400 ] ]
         : [ 'status' => 201, 'data' => [ 'sid' => 'SM1f0e8ae6ade43cb3c0ce4525424e404f', 'status' => 'queued' ] ];
  } ] );

  $calls = function () {
    $made = [];
    foreach ( padCurlRecorded () as $call ) {
      parse_str ( (string) $call ['post'], $body );
      $made [] = [ 'call' => $call ['method'] . ' ' . $call ['url'], 'type' => $call ['headers'] ['Content-Type'] ?? '',
                   'user' => strtok ( (string) base64_decode ( substr ( $call ['headers'] ['Authorization'] ?? '', 6 ) ), ':' ), 'body' => $body ];
    }
    padCurlFake ( [] );
    return $made;
  };

  $results = fn () => array_map ( fn ( $one ) => $one ['ok'] ? 'sent' : $one ['error'], padNotifyResults () );
  $data    = [ 'order' => 42, 'items' => 3, 'name' => 'Ann', 'url' => 'https://shop.example/orders/42' ];

  padNotify ( [ 'id' => 7, 'phone' => '+31 (6) 1234-5678' ], 'shipped', $data, [ 'twilio' ] );

  $report ['sent'] = [ 'calls' => $calls (), 'results' => $results () ];

  padNotify ( [ 'id' => 7, 'phone' => '+31612345678' ], 'shipped', $data, [ 'twilio' ],
              [ 'twilio' => [ 'from' => 'MG9752274e9e519418a7406176694466fa', 'key' => $twilioKey,
                              'token' => str_repeat ( 'k', 32 ) ] ] );

  $call = $calls () [0];

  $report ['service'] = [ 'user' => $call ['user'], 'body' => $call ['body'], 'results' => $results () ];

  padNotify ( [ 'id' => 8, 'phone' => '06 1234 5678' ], 'shipped', $data, [ 'twilio' ] );

  $report ['no country'] = [ 'calls' => count ( $calls () ), 'results' => $results () ];

  padNotify ( [ 'id' => 9, 'phone' => '+1 500 555 0001' ], 'shipped', $data, [ 'twilio' ] );

  $report ['refused'] = [ 'calls' => count ( $calls () ), 'results' => $results () ];

  padNotify ( [ 'id' => 7, 'phone' => '+31612345678' ], 'long', [ 'text' => str_repeat ( 'x', 1700 ), 'title' => 'Long' ], [ 'twilio' ] );

  $body = $calls () [0] ['body'] ['Body'];

  $report ['cut'] = [ 'length' => mb_strlen ( $body ), 'ends' => substr ( $body, -5 ), 'results' => $results () ];

  $json = json_encode ( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

  // Keep the expected answer free of credential-shaped strings. Only the exact dummy
  // values are labelled, so an incorrect URL or authentication user still fails.
  $json = str_replace ( [ $twilioAccount, $twilioKey ], [ '[test-account-sid]', '[test-api-key]' ], $json );

?>
