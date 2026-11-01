<?php

  // Slack and ntfy are not asked here: a fake answers every request, as a test answers them,
  // and keeps what was sent. The mail goes to DATA/mail/ - the file transport. In an
  // application the addresses stand in .env: SLACK_WEBHOOK_URL, NTFY_TOPIC.

  padCurlFake ( [ '*' => 'ok' ] );

  $ann = [ 'id' => 'manual-ann', 'email' => 'ann@example.com', 'ntfy' => 'ann-orders' ];

  padNotify ( $ann, 'order-shipped', [ 'order' => 42, 'name' => 'Ann', 'url' => 'https://shop.example/orders/42' ],
              [ 'mail', 'slack', 'ntfy' ], [ 'slack' => [ 'url' => 'https://hooks.slack.com/services/T0/B0/example' ] ] );

  $results = [];

  foreach ( padNotifyResults () as $channel => $result )
    $results [] = [ 'channel' => $channel, 'ok' => $result ['ok'] ? 'sent' : $result ['error'] ];

  $subject = $padMailLast ['subject'];
  $slack   = json_decode ( padCurlRecorded () [0] ['post'], TRUE ) ['text'];
  $ntfy    = padCurlRecorded () [1] ['post'];

  padCurlFakeStop ();

?>
