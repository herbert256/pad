<?php

  // The webhook channel's address is its credential - a catch hook of Zapier, Make or n8n
  // runs for anyone holding it - so the dumps and the error report (padRedact, $padCurlLast
  // in them) and the address _events/curl.php hears show it redacted, a service of the
  // application's own too: the address the channel posted to is redacted by value for the
  // rest of the request, from its path on - whole when its path is shorter than eight
  // characters. Zapier's, Make's and n8n's are known by their form in any text, and a
  // webhook's address under its usual name, WEBHOOK_URL, by the name. The fetch itself goes
  // to the address as it is.

  $GLOBALS ['heardCurlUrls'] = [];

  $padCurlLast = NULL;

  padCurlFake ( [ '*' => 204 ] );

  $_ENV ['WEBHOOK_SECRET'] = 'whsec_' . base64_encode ( str_repeat ( "\x01", 24 ) );

  $data = [ 'order' => 42, 'items' => 1, 'name' => 'Ann', 'url' => '' ];

  padNotify ( [ 'id' => 7 ], 'shipped', $data, [ 'webhook' ], [ 'webhook' => [ 'url' => 'https://automate.example.org/in/7f3c2a91e5/order?src=pad' ] ] );

  $last = $padCurlLast ['url'] ?? '';

  padNotify ( [ 'id' => 8 ], 'shipped', $data, [ 'webhook' ], [ 'webhook' => [ 'url' => 'https://short.example.net/x1' ] ] );

  $report = [
    'fetched'  => array_column ( padCurlRecorded (), 'url' ),
    'the hook' => $GLOBALS ['heardCurlUrls'],
    'a report' => padRedact ( [
      'last'        => $last,
      'text'        => 'The webhook did not answer (https://automate.example.org/in/7f3c2a91e5/order?src=pad)',
      'short'       => 'POST https://short.example.net/x1',
      'zapier'      => 'https://hooks.zapier.com/hooks/catch/1234567/abcdef1/',
      'make'        => 'https://hook.eu1.make.com/k3j4h5g6f7d8s9a0q1w2e3r4t5y6u7i8',
      'n8n'         => 'https://n8n.example.com/webhook/0f9c1d6e-5a7b-4c3d-9e8f-1a2b3c4d5e6f',
      'n8n test'    => 'https://n8n.example.com/webhook-test/orders/new?x=1',
      'WEBHOOK_URL' => 'https://hooks.example.net/pad',
      'plain'       => 'https://api.example.com/v1/items?page=2' ] ) ];

  $json = json_encode ( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );

?>
