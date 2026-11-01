<?php

  // Slack, Discord, Teams, Telegram, ntfy, Matrix and the SMS gateway, each answered by a fake:
  // what each one was sent - the method, the address, the headers that matter and the body.
  // Their settings come from the options here, as they would from .env; the recipient's own
  // fields - a phone, a Telegram chat - from the row. Slack has a section of its own in the
  // template, the others get the plain text; a value in it ({$name}) is text everywhere.

  $user = [ 'id' => 7, 'phone' => '+31 6 1234 5678', 'telegram' => '123456789', 'ntfy' => 'ann-orders' ];

  padCurlFake ( [ '*' => [ 'status' => 200, 'data' => [ 'ok' => TRUE ] ] ] );

  $ok = padNotify ( $user, 'shipped', [ 'order' => 42, 'items' => 3, 'name' => 'Ann <ann> {php:getcwd}', 'url' => 'https://shop.example/orders/42' ],
                    [ 'slack', 'discord', 'teams', 'telegram', 'ntfy', 'matrix', 'sms' ],
                    [ 'slack'    => [ 'url'   => 'https://hooks.slack.com/services/T0/B0/x' ],
                      'discord'  => [ 'url'   => 'https://discord.com/api/webhooks/1/abc' ],
                      'teams'    => [ 'url'   => 'https://prod-01.westeurope.logic.azure.com/workflows/w1/triggers/manual/paths/invoke?sig=s' ],
                      'telegram' => [ 'token' => '110201543:AAHdqTcvCH1vGWJxfSeofSAs0K5PALDsaw' ],
                      'ntfy'     => [ 'token' => 'tk_ntfy' ],
                      'matrix'   => [ 'server' => 'https://matrix.example.org', 'token' => 'syt_x', 'room' => '!room:example.org' ],
                      'sms'      => [ 'url'   => 'https://sms.example.com/v1/messages', 'token' => 'smskey', 'from' => 'Shop' ] ] );

  $calls = [];

  foreach ( padCurlRecorded () as $call )
    $calls [] = [
      'call'    => $call ['method'] . ' ' . preg_replace ( '#/send/m\.room\.message/pad[0-9a-f]{16}$#', '/send/m.room.message/<txn>', $call ['url'] ),
      'headers' => array_intersect_key ( $call ['headers'], array_flip ( [ 'Content-Type', 'Authorization', 'Title', 'Click' ] ) ),
      'body'    => json_decode ( $call ['post'], TRUE ) ?? $call ['post'] ];

  $json = json_encode ( [ 'ok' => $ok, 'results' => array_map ( fn ( $one ) => $one ['ok'], padNotifyResults () ), 'calls' => $calls ],
                        JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

?>
