<?php

  // A secret some services put in the address - a Telegram bot's token in its path, the
  // webhooks of Slack, Discord and Teams whose address is their credential, a signature in a
  // query (sig=, a Teams workflow's and Azure's) - is treated as the ones that travel in a
  // header: the dumps and the error report (padRedact, $padCurlLast in them) and the address
  // _events/curl.php is handed - the place to log an API that is slow or down - show the
  // service and its path up to the secret, never the secret. The fetch itself goes to the
  // address as it is.

  $padCurlLast = NULL;

  padCurlFake ( [ '*' => 'ok' ] );

  padNotify ( [ 'id' => 7, 'telegram' => '123456789' ], 'shipped', [ 'order' => 42, 'items' => 1, 'name' => 'Ann', 'url' => '' ], [ 'telegram', 'slack', 'teams' ],
              [ 'telegram' => [ 'token' => '110201543:AAHdqTcvCH1vGWJxfSeofSAs0K5PALDsaw' ],
                'slack'    => [ 'url'   => 'https://hooks.slack.com/services/T000/B000/XXXXXXXXXXXXXXXXXXXXXXXX' ],
                'teams'    => [ 'url'   => 'https://prod-01.westeurope.logic.azure.com:443/workflows/w1/triggers/manual/paths/invoke?api-version=2016-06-01&sp=%2Ftriggers%2Fmanual%2Frun&sv=1.0&sig=AbCdEf123' ] ] );

  $report = [
    'fetched'  => array_column ( padCurlRecorded (), 'url' ),
    'the hook' => $GLOBALS ['heardCurlUrls'] ?? [],
    'a report' => padRedact ( [
      'last'    => $padCurlLast ['url'] ?? '',
      'discord' => 'https://discord.com/api/webhooks/1234567890/abcDEF-ghi_JKL',
      'office'  => 'https://contoso.webhook.office.com/webhookb2/a1b2@c3d4/IncomingWebhook/e5f6/g7h8',
      'file'    => 'https://api.telegram.org/file/bot110201543:AAHdqTcvCH1vGWJxfSeofSAs0K5PALDsaw/photos/1.jpg',
      'text'    => 'Slack answered for https://hooks.slack.com/services/T000/B000/XXXXXXXXXXXXXXXXXXXXXXXX: invalid_token',
      'plain'   => 'https://api.example.com/v1/items?page=2&sort=name' ] ) ];

  $json = json_encode ( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );

?>
