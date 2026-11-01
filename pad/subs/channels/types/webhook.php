<?php

  // webhook - the notification as JSON posted to an address of the application's choosing -
  // Zapier, n8n, a service of its own - and signed as Standard Webhooks has it
  // (standardwebhooks.com): the address in WEBHOOK_URL, or the option url - never a field of
  // the recipient - and the secret in WEBHOOK_SECRET, whsec_ and the base64 of 24 to 64
  // random bytes as the spec has a symmetric one (the prefix optional), or several split by
  // spaces while a new one is rolled in. The headers: webhook-id - the notification's id, msg_
  // and 24 hex digits, which a queued notification keeps for its retries, so a receiver drops
  // a second delivery of the same message - webhook-timestamp, the Unix time of this attempt,
  // and webhook-signature, v1, and the HMAC-SHA256 of id.timestamp.body for every secret
  // (_lib/webhook.php). The body is the spec's: type - notification.<name> - timestamp - when
  // padNotify made it, ISO 8601 in UTC - and data: the name, the title, the @webhook@ section
  // or the plain text as text and html, the url, the recipient's id and email and the values
  // padNotify was handed. A 2xx is delivered; 410, the spec's word for an endpoint that wants
  // nothing more, is said so. The address is a credential - a catch hook of Zapier or n8n
  // runs for anyone holding it, the signature unchecked - so the reports and _events/curl.php
  // see it redacted, whatever its service (secret: by value, from its path on).

  $padWebhookSecret = '(?:whsec_)?(?:(?:[A-Za-z0-9+\/]{4}){8,20}(?:[A-Za-z0-9+\/]{2}==|[A-Za-z0-9+\/]{3}=)?|(?:[A-Za-z0-9+\/]{4}){21}(?:[A-Za-z0-9+\/]{2}==)?)';

  return [
    'settings' => [ 'url'    => [ 'env' => 'WEBHOOK_URL', 'address' => TRUE, 'secret' => TRUE ],
                    'secret' => [ 'env' => 'WEBHOOK_SECRET', 'pattern' => "/^$padWebhookSecret( $padWebhookSecret)*$/D", 'secret' => TRUE,
                                  'is' => 'Standard Webhooks secret - whsec_ and the base64 of 24 to 64 bytes, several split by spaces' ] ],
    'send'     => function ( $message, $settings, $recipient ) {

      include_once PAD . 'subs/channels/_lib/webhook.php';

      $keys = array_map ( 'padWebhookSecret', explode ( ' ', (string) $settings ['secret'] ) );

      if ( in_array ( NULL, $keys, TRUE ) )
        return 'a secret is no base64 of 24 to 64 bytes';

      $body = json_encode ( [ 'type'      => 'notification.' . $message ['name'],
                              'timestamp' => gmdate ( 'Y-m-d\TH:i:s\Z', $message ['at'] ),
                              'data'      => [ 'name'      => $message ['name'], 'title' => $message ['title'], 'text' => $message ['text'],
                                               'html'      => $message ['html'], 'url'   => $message ['url'],
                                               'recipient' => (object) array_intersect_key ( (array) $recipient, [ 'id' => 1, 'email' => 1 ] ),
                                               'values'    => (object) $message ['data'] ] ],
                            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR );

      $time   = (string) (int) padNow ( 'U' );
      $answer = padNotifyHttp ( 'POST', $settings ['url'], (string) $body, [
                  'Content-Type'      => 'application/json',
                  'webhook-id'        => $message ['id'],
                  'webhook-timestamp' => $time,
                  'webhook-signature' => padWebhookSignature ( $message ['id'], $time, (string) $body, $keys ) ] );

      return ( $answer ['status'] == 410 ) ? 'the endpoint answered 410 Gone - it asks to be sent nothing more' : padNotifyAnswer ( $answer, 'The webhook' );

    },
  ];

?>
