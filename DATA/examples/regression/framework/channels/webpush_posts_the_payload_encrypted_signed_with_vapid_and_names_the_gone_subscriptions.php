<?php

  // A push to two browsers of one person: the VAPID keys made by padWebPushKeys, each browser's
  // subscription as PushSubscription.toJSON () gives it - its key pair made here, so the test
  // can open what was sent as the browser would. The first push service takes it (201); the
  // second answers 410, its subscription is gone. The VAPID token is checked with the public
  // key, its audience is the push service's origin.

  include_once PAD . 'subs/channels/_lib/webpush.php';

  $vapid = padWebPushKeys ();

  [ $d1, $p1 ] = padEcNew ();
  $auth1       = random_bytes ( 16 );

  $user = [ 'id' => 7, 'webpush' => [
    [ 'endpoint' => 'https://fcm.googleapis.com/fcm/send/abc123', 'keys' => [ 'p256dh' => padCryptB64 ( $p1 ), 'auth' => padCryptB64 ( $auth1 ) ] ],
    json_encode ( [ 'endpoint' => 'https://updates.push.services.mozilla.com/wpush/v2/gone', 'keys' => [ 'p256dh' => padCryptB64 ( $p1 ), 'auth' => padCryptB64 ( $auth1 ) ] ] ) ] ];

  padCurlFake ( [ 'https://fcm.googleapis.com/*' => 201, 'https://updates.push.services.mozilla.com/*' => 410 ] );

  $ok = padNotify ( $user, 'shipped', [ 'order' => 42, 'items' => 3, 'name' => 'Ann', 'url' => 'https://shop.example/orders/42' ], [ 'webpush' ],
                    [ 'webpush' => [ 'public' => $vapid ['public'], 'private' => $vapid ['private'], 'subject' => 'mailto:shop@example.com' ] ] );

  $call    = padCurlRecorded () [0];
  $payload = padWebPushDecrypt ( $call ['post'], $d1, $p1, $auth1 );
  $jwt     = preg_match ( '/^vapid t=([^,]+), k=(\S+)$/', $call ['headers'] ['Authorization'], $m ) ? $m [1] : '';

  $json = json_encode ( [
    'ok'       => $ok,
    'results'  => padNotifyResults (),
    'keys'     => [ strlen ( padCryptUnB64 ( $vapid ['public'] ) ), strlen ( padCryptUnB64 ( $vapid ['private'] ) ) ],
    'request'  => [ $call ['method'], $call ['url'], $call ['headers'] ['Content-Encoding'], $call ['headers'] ['Content-Type'], $call ['headers'] ['TTL'] ],
    'vapid'    => [ 'k is the public key' => ( $m [2] ?? '' ) === $vapid ['public'],
                    'verified'            => padJwtVerify ( $jwt, padEcPublicPem ( padCryptUnB64 ( $vapid ['public'] ) ) ),
                    'claims'              => array_intersect_key ( padJwtClaims ( $jwt ) ?? [], [ 'aud' => 1, 'sub' => 1 ] ) ],
    'opened'   => json_decode ( (string) $payload, TRUE ),
  ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );

?>
