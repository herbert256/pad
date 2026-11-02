<?php

  // The address a notification is posted to - the webhook of Slack, Discord and Teams - comes
  // from the configuration (.env here, or the options), never from the recipient's row: a
  // profile field holding an address of the server's own network, or a users table with a
  // column that shares a channel's name, changes nothing. The row names the person within
  // the channel alone - a Telegram chat by its number, an ntfy topic, a Matrix room by its id
  // - each checked, and a push subscription's endpoint must be on a push service: a value
  // that is none is the data's, the channel not sent, nothing fetched. A configured address
  // without a scheme is the author's - padCurl would read it as a page or a _data/ file.

  include_once PAD . 'subs/channels/_lib/webpush.php';

  $_ENV ['SLACK_WEBHOOK_URL']   = 'https://hooks.slack.com/services/T0/B0/configured';
  $_ENV ['DISCORD_WEBHOOK_URL'] = 'https://discord.com/api/webhooks/1/configured';
  $_ENV ['TEAMS_WEBHOOK_URL']   = 'https://prod-01.westeurope.logic.azure.com/workflows/w1/triggers/manual/paths/invoke?sig=configured';
  $_ENV ['TELEGRAM_BOT_TOKEN']  = '110201543:AAHdqTcvCH1vGWJxfSeofSAs0K5PALDsaw';
  $_ENV ['MATRIX_TOKEN']        = 'syt_x';

  padCurlFake ( [ 'https://hooks.slack.com/*' => 'ok', 'https://discord.com/*' => 204, 'https://prod-01.westeurope.logic.azure.com/*' => 202,
                  'https://api.telegram.org/*' => 'ok', 'https://ntfy.sh/*' => 'ok', 'https://matrix.example.org/*' => 'ok',
                  '*' => [ 'status' => 403, 'data' => 'ami-id ami-0abc instance-id i-0123 iam/security-credentials/web-role' ] ] );

  $calls = function () {
    $urls = array_column ( padCurlRecorded (), 'url' );
    padCurlFake ( [] );
    return $urls;
  };

  $results = fn () => array_map ( fn ( $one ) => $one ['ok'] ? 'sent' : $one ['error'], padNotifyResults () );

  $data = [ 'order' => 42, 'items' => 1, 'name' => 'Ann', 'url' => '' ];

  // 1. Addresses of the network in the fields of a profile.

  padNotify ( [ 'id' => 7, 'slack' => 'http://169.254.169.254/latest/meta-data/', 'discord' => 'http://127.0.0.1:6379/',
                'teams' => 'http://10.0.0.5:8500/v1/agent/self' ], 'shipped', $data, [ 'slack', 'discord', 'teams' ] );

  $report ['profile'] = [ 'fetched' => $calls (), 'results' => $results () ];

  // 2. A users table whose columns share the channels' names - a team, a handle.

  padNotify ( [ 'id' => 8, 'teams' => 'Sales', 'slack' => '@ann' ], 'shipped', $data, [ 'slack', 'teams' ] );

  $report ['columns'] = [ 'fetched' => $calls (), 'results' => $results () ];

  // 3. The fields that name the person there, holding something else.

  [ , $point ] = padEcNew ();

  $vapid = padWebPushKeys ();

  padNotify ( [ 'id' => 9, 'telegram' => '@ann; DROP', 'ntfy' => '../admin', 'matrix' => '#lobby:example.org',
                'webpush' => [ 'endpoint' => 'https://169.254.169.254/latest/meta-data/',
                               'keys' => [ 'p256dh' => padCryptB64 ( $point ), 'auth' => padCryptB64 ( random_bytes ( 16 ) ) ] ] ],
              'shipped', $data, [ 'telegram', 'ntfy', 'matrix', 'webpush' ],
              [ 'webpush' => [ 'public' => $vapid ['public'], 'private' => $vapid ['private'], 'subject' => 'mailto:shop@example.com' ] ] );

  $report ['names'] = [ 'fetched' => $calls (), 'results' => $results () ];

  // 4. The same fields holding what they should: a chat, a topic, a room.

  padNotify ( [ 'id' => 10, 'telegram' => '123456789', 'ntfy' => 'ann-orders', 'matrix' => '!room:example.org' ],
              'shipped', $data, [ 'telegram', 'ntfy', 'matrix' ], [ 'matrix' => [ 'server' => 'https://matrix.example.org' ] ] );

  $report ['fine'] = [ 'fetched' => array_map ( fn ( $url ) => preg_replace ( '#/m\.room\.message/pad[0-9a-f]{16}$#', '/m.room.message/<txn>', $url ), $calls () ),
                       'results' => $results () ];

  // 5. A configured address without a scheme - the strict check off, to read the result.

  $padCheckSyntax = FALSE;

  padNotify ( [ 'id' => 11 ], 'shipped', $data, [ 'slack' ], [ 'slack' => [ 'url' => 'my-slack-hook' ] ] );

  $report ['no scheme'] = [ 'fetched' => $calls (), 'results' => $results () ];

  $json = json_encode ( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );

?>
