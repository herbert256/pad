<?php

  // The webhook channel, signed as Standard Webhooks has it. The signature is first checked on
  // the worked example of Svix's "verifying manually" page - its secret, id, timestamp and
  // payload give its v1 signature. Then a notification is posted to the configured address,
  // the clock frozen: the spec's body (type, timestamp, data), the webhook-id of the
  // notification, the timestamp of the attempt and a v1 signature per secret of a rotation,
  // each verified here as a receiver does it. Queued, the first attempt answered 500, the
  // retry carries the same webhook-id - a receiver drops a second delivery by it - and a 410
  // is said to be the spec's Gone.

  include_once PAD . 'subs/channels/_lib/webhook.php';

  $report ['svix example'] = padWebhookSignature ( 'msg_loFOjxBNrRLzqYUf', '1731705121', '{"event_type":"ping","data":{"success":true}}',
                                                   [ base64_decode ( 'plJ3nmyCDGBKInavdOK15jsl' ) ] );

  $old = 'whsec_' . base64_encode ( str_repeat ( "\x01", 24 ) );
  $new = 'whsec_' . base64_encode ( str_repeat ( "\x02", 32 ) );

  $_ENV ['WEBHOOK_URL']    = 'https://hooks.example.com/pad';
  $_ENV ['WEBHOOK_SECRET'] = "$new $old";

  padNowFreeze ( '2026-11-01 12:00:00 UTC' );

  $verify = function ( $call, $secret ) {
    $key  = base64_decode ( substr ( $secret, 6 ) );
    $want = 'v1,' . base64_encode ( hash_hmac ( 'sha256', $call ['headers'] ['webhook-id'] . '.' . $call ['headers'] ['webhook-timestamp'] . '.' . $call ['post'], $key, TRUE ) );
    return in_array ( $want, explode ( ' ', $call ['headers'] ['webhook-signature'] ), TRUE );
  };

  padCurlFake ( [ 'https://hooks.example.com/*' => 204 ] );

  padNotify ( [ 'id' => 7, 'email' => 'ann@example.com', 'phone' => '+31612345678' ], 'shipped',
              [ 'order' => 42, 'items' => 3, 'name' => 'Ann', 'url' => 'https://shop.example/orders/42' ], [ 'webhook' ] );

  $call = padCurlRecorded () [0];

  $report ['sent'] = [ 'call'       => $call ['method'] . ' ' . $call ['url'],
                       'type'       => $call ['headers'] ['Content-Type'],
                       'id'         => (bool) preg_match ( '/^msg_[0-9a-f]{24}$/D', $call ['headers'] ['webhook-id'] ),
                       'timestamp'  => $call ['headers'] ['webhook-timestamp'],
                       'signatures' => count ( explode ( ' ', $call ['headers'] ['webhook-signature'] ) ),
                       'new secret' => $verify ( $call, $new ),
                       'old secret' => $verify ( $call, $old ),
                       'body'       => json_decode ( $call ['post'], TRUE ),
                       'results'    => array_map ( fn ( $one ) => $one ['ok'] ? 'sent' : $one ['error'], padNotifyResults () ) ];

  // Queued: the first attempt fails, the retry is the same message.

  padQueueFlush ( 'notifyhook' );

  $tries = 0;

  padCurlFake ( [ 'https://hooks.example.com/*' => function () use ( &$tries ) {
    return ++$tries == 1 ? 500 : 200;
  } ] );

  padNotify ( [ 'id' => 8 ], 'shipped', [ 'order' => 43, 'items' => 1, 'name' => 'Bo', 'url' => '' ], [ 'webhook' ], [ 'queue' => 'notifyhook' ] );

  $first = padQueueWork ( 'notifyhook', 0, 60, 3 );

  foreach ( glob ( DATA . 'queue/regression/framework/notifyhook/*.job' ) as $file ) {
    $record = json_decode ( file_get_contents ( $file ), TRUE );
    $record ['available'] = time () - 1;
    file_put_contents ( $file, json_encode ( $record ) );
    rename ( $file, dirname ( $file ) . '/' . sprintf ( '%010d', time () - 1 ) . '-' . $record ['id'] . '.job' );
  }

  $second = padQueueWork ( 'notifyhook', 0, 60, 3 );
  $ids    = array_map ( fn ( $one ) => $one ['headers'] ['webhook-id'], padCurlRecorded () );

  padQueueFlush ( 'notifyhook' );

  $report ['queued'] = [ 'attempts' => count ( $ids ), 'first' => [ $first ['done'], $first ['retried'] ], 'second' => [ $second ['done'], $second ['retried'] ],
                         'one id' => count ( array_unique ( $ids ) ) == 1 ];

  padCurlFake ( [ 'https://hooks.example.com/*' => 410 ] );

  padNotify ( [ 'id' => 9 ], 'shipped', [ 'order' => 44, 'items' => 1, 'name' => 'Cy', 'url' => '' ], [ 'webhook' ] );

  $report ['gone'] = padNotifyResults () ['webhook'] ['error'];

  padNowFreeze ();

  $json = json_encode ( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

?>
