<?php

  // The webhook channel posts the notification as JSON, signed as Standard Webhooks has it. A
  // fake answers here and keeps what was sent; below it the receiver's side checks the
  // signature as any receiver does - the HMAC-SHA256 of id.timestamp.body under the secret.
  // In an application the address and the secret stand in .env: WEBHOOK_URL, WEBHOOK_SECRET.

  padCurlFake ( [ '*' => 204 ] );

  $signingKey = 'whsec_MfKQ9r8GKYqrTwjUPD8ILPZIo2LhLjHd';

  padNotify ( [ 'id' => 'manual-ann', 'email' => 'ann@example.com' ], 'order-shipped',
              [ 'order' => 42, 'name' => 'Ann', 'url' => 'https://shop.example/orders/42' ],
              [ 'webhook' ], [ 'webhook' => [ 'url' => 'https://hooks.example.com/orders', 'secret' => $signingKey ] ] );

  $sent = padCurlRecorded () [0];
  $head = $sent ['headers'];
  $body = json_decode ( $sent ['post'], TRUE );

  // The receiver: the signature made again from the headers and the body as they came.

  $signed = $head ['webhook-id'] . '.' . $head ['webhook-timestamp'] . '.' . $sent ['post'];
  $mac    = 'v1,' . base64_encode ( hash_hmac ( 'sha256', $signed, base64_decode ( substr ( $signingKey, 6 ) ), TRUE ) );
  $valid  = hash_equals ( $mac, $head ['webhook-signature'] ) ? 'yes' : 'no';

  $type  = $body ['type'];
  $title = $body ['data'] ['title'];

  padCurlFakeStop ();

?>
