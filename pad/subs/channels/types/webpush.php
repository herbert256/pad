<?php

  // webpush - a push message to the browsers the person allowed to notify them, also with the
  // site closed: the recipient's field webpush holds the subscription the browser gave -
  // PushSubscription.toJSON (), as an array or its JSON - or a list of them. The message is
  // encrypted for each (RFC 8291, aes128gcm) and posted to its push service signed with the
  // application's VAPID keys (RFC 8292): VAPID_PUBLIC_KEY, VAPID_PRIVATE_KEY - padWebPushKeys ()
  // makes a pair - and VAPID_SUBJECT, a mailto: or https: address the service can reach the
  // sender at. The payload is JSON - title, body (the @webpush@ section, or the plain text),
  // tag (the notification's name) and url - for the page's service worker to show. A
  // subscription its service answers 404 or 410 for is gone: padNotifyResults lists it under
  // gone, for the application to forget.

  return [
    'settings' => [ 'public'       => [ 'env' => 'VAPID_PUBLIC_KEY' ],
                    'private'      => [ 'env' => 'VAPID_PRIVATE_KEY' ],
                    'subject'      => [ 'env' => 'VAPID_SUBJECT' ],
                    'subscription' => [ 'to'  => 'webpush' ] ],
    'send'     => function ( $message, $settings, $recipient, $options ) {

      include_once PAD . 'subs/channels/_lib/webpush.php';

      $list = $settings ['subscription'];
      $list = ( is_array ( $list ) and array_is_list ( $list ) ) ? $list : [ $list ];

      $payload = json_encode ( [ 'title' => $message ['title'], 'body' => mb_strimwidth ( $message ['text'], 0, 1000, '...', 'UTF-8' ),
                                 'tag'   => $message ['name'],  'url'  => $message ['url'] ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

      $sent = 0;
      $gone = [];
      $fail = [];

      foreach ( $list as $one ) {

        $subscription = padWebPushSubscription ( $one );

        if ( $subscription === NULL ) {
          $fail [] = 'a subscription is no PushSubscription - endpoint, keys.p256dh and keys.auth';
          continue;
        }

        $body  = padWebPushEncrypt ( $payload, $subscription ['p256dh'], $subscription ['auth'] );
        $vapid = padWebPushVapid ( $subscription ['endpoint'], $settings ['public'], $settings ['private'], $settings ['subject'] );

        if ( $body === '' or $vapid === '' ) {
          $fail [] = ( $body === '' ) ? 'the message could not be encrypted - too long?' : 'the VAPID keys are no P-256 key pair in base64url';
          continue;
        }

        $answer = padNotifyHttp ( 'POST', $subscription ['endpoint'], $body, [
          'Authorization'    => $vapid,
          'Content-Encoding' => 'aes128gcm',
          'Content-Type'     => 'application/octet-stream',
          'TTL'              => (string) max ( 0, (int) ( $options ['webpush'] ['ttl'] ?? 86400 ) ),
          'Urgency'          => 'normal' ] );

        if ( $answer ['status'] == 404 or $answer ['status'] == 410 )
          $gone [] = $subscription ['endpoint'];
        elseif ( ( $said = padNotifyAnswer ( $answer, 'The push service' ) ) === TRUE )
          $sent++;
        else
          $fail [] = $said;

      }

      if ( ! $fail and ! $sent )
        $fail [] = 'every subscription is gone';

      return [ 'ok' => ! $fail, 'error' => implode ( '; ', array_unique ( $fail ) ), 'gone' => $gone ];

    },
  ];

?>
