<?php

  // Web push without a library: the message encrypted for the browser's subscription as RFC
  // 8291 has it (aes128gcm, RFC 8188) and the request signed for the push service with VAPID
  // (RFC 8292) - openssl's ECDH, HKDF (hash_hkdf) and AES-128-GCM, and an ES256 JWT
  // (subs/oauth/_lib/jwt.php). For the webpush channel, subs/channels/types/webpush.php.
  //
  // padWebPushNewKeys      a VAPID key pair: the public key (65 bytes) and the private one (32),
  //                        base64url - VAPID_PUBLIC_KEY and VAPID_PRIVATE_KEY, the public one
  //                        also what the page hands to pushManager.subscribe
  // padWebPushEncrypt      a payload encrypted for a subscription's p256dh key and auth secret:
  //                        the body to post - salt, record size, the server's key, the cipher
  // padWebPushDecrypt      the way back with the browser's private key - what a browser does;
  //                        here for the tests, which prove the encryption both ways
  // padWebPushSubscription a subscription as the browser's PushSubscription.toJSON () gives it -
  //                        an array or its JSON - as [ endpoint, p256dh, auth ], the keys raw
  // padWebPushVapid        the Authorization header of a push: vapid t=<JWT>, k=<public key>
  //
  // The encryption takes a fresh key pair and a random salt for every message, so the same
  // message is new bytes every time; with the salt and the key pair given it is RFC 8291's
  // own example, byte for byte - the framework case channels/webpush_* proves that.

  include_once PAD . 'subs/oauth/_lib/jwt.php';

  const padWebPushRecord = 4096;

  function padWebPushNewKeys () {

    [ $d, $point ] = padEcNew ();

    return [ 'public' => padCryptB64 ( $point ), 'private' => padCryptB64 ( $d ) ];

  }

  function padWebPushEncrypt ( $plaintext, $p256dh, $auth, $salt = NULL, $server = NULL ) {

    [ $d, $asPublic ] = $server ?? padEcNew ();

    $salt ??= random_bytes ( 16 );

    if ( strlen ( $p256dh ) != 65 or strlen ( $auth ) != 16 or strlen ( $salt ) != 16 or strlen ( $plaintext ) > padWebPushRecord - 103 )
      return '';

    $secret = openssl_pkey_derive ( padEcPublicPem ( $p256dh ), padEcPem ( $d, $asPublic ) );

    if ( ! is_string ( $secret ) or strlen ( $secret ) != 32 )
      return '';

    $ikm    = hash_hkdf ( 'sha256', $secret, 32, "WebPush: info\0" . $p256dh . $asPublic, $auth );
    $cek    = hash_hkdf ( 'sha256', $ikm, 16, "Content-Encoding: aes128gcm\0", $salt );
    $nonce  = hash_hkdf ( 'sha256', $ikm, 12, "Content-Encoding: nonce\0", $salt );
    $cipher = openssl_encrypt ( $plaintext . "\x02", 'aes-128-gcm', $cek, OPENSSL_RAW_DATA, $nonce, $tag );

    if ( $cipher === FALSE )
      return '';

    return $salt . pack ( 'N', padWebPushRecord ) . chr ( 65 ) . $asPublic . $cipher . $tag;

  }

  function padWebPushDecrypt ( $body, $uaPrivate, $uaPublic, $auth ) {

    if ( strlen ( $body ) < 86 + 17 or ord ( $body [20] ) != 65 )
      return NULL;

    $salt     = substr ( $body, 0, 16 );
    $asPublic = substr ( $body, 21, 65 );
    $cipher   = substr ( $body, 86, -16 );
    $tag      = substr ( $body, -16 );
    $secret   = openssl_pkey_derive ( padEcPublicPem ( $asPublic ), padEcPem ( $uaPrivate, $uaPublic ) );

    if ( ! is_string ( $secret ) )
      return NULL;

    $ikm   = hash_hkdf ( 'sha256', $secret, 32, "WebPush: info\0" . $uaPublic . $asPublic, $auth );
    $cek   = hash_hkdf ( 'sha256', $ikm, 16, "Content-Encoding: aes128gcm\0", $salt );
    $nonce = hash_hkdf ( 'sha256', $ikm, 12, "Content-Encoding: nonce\0", $salt );
    $plain = openssl_decrypt ( $cipher, 'aes-128-gcm', $cek, OPENSSL_RAW_DATA, $nonce, $tag );

    if ( ! is_string ( $plain ) )
      return NULL;

    $plain = rtrim ( $plain, "\0" );

    return str_ends_with ( $plain, "\x02" ) ? substr ( $plain, 0, -1 ) : NULL;

  }

  function padWebPushSubscription ( $subscription ) {

    if ( is_string ( $subscription ) )
      $subscription = json_decode ( $subscription, TRUE );

    if ( ! is_array ( $subscription ) )
      return NULL;

    $endpoint = $subscription ['endpoint'] ?? NULL;
    $p256dh   = padCryptUnB64 ( rtrim ( (string) ( $subscription ['keys'] ['p256dh'] ?? $subscription ['p256dh'] ?? '' ), '=' ) );
    $auth     = padCryptUnB64 ( rtrim ( (string) ( $subscription ['keys'] ['auth']   ?? $subscription ['auth']   ?? '' ), '=' ) );

    if ( ! is_string ( $endpoint ) or ! preg_match ( '~^https://[^\s/?#]+/\S*$~D', $endpoint )
         or $p256dh === NULL or strlen ( $p256dh ) != 65 or $auth === NULL or strlen ( $auth ) != 16 )
      return NULL;

    return [ 'endpoint' => $endpoint, 'p256dh' => $p256dh, 'auth' => $auth ];

  }

  // Whether a subscription's endpoint is on a push service: the endpoint is the browser's to
  // give, so the recipient's row holds it, and this server posts to it - to the services of
  // the browsers alone, Chrome's and the browsers built on it (FCM), Firefox's, Edge's (WNS)
  // and Safari's, or a host $more names (WEBPUSH_SERVICES, comma separated; *.example.org for
  // its subdomains). A plain host, https, no port, no login.

  const padWebPushServices = [ 'fcm.googleapis.com', 'updates.push.services.mozilla.com', '*.push.services.mozilla.com',
                               '*.notify.windows.com', 'web.push.apple.com', '*.push.apple.com' ];

  function padWebPushService ( $endpoint, $more = '' ) {

    if ( ! preg_match ( '~^https://([a-z0-9.-]+)(:443)?/[^\s\\\\]*$~iD', (string) $endpoint, $match ) )
      return FALSE;

    $host = strtolower ( rtrim ( $match [1], '.' ) );

    foreach ( array_merge ( padWebPushServices, preg_split ( '/[\s,]+/', strtolower ( (string) $more ), -1, PREG_SPLIT_NO_EMPTY ) ) as $service )
      if ( $host === $service or ( str_starts_with ( $service, '*.' ) and str_ends_with ( $host, substr ( $service, 1 ) ) ) )
        return TRUE;

    return FALSE;

  }

  // RFC 8292: a JWT for the push service's origin, twelve hours long, signed with the VAPID
  // private key; the public key beside it.

  function padWebPushVapid ( $endpoint, $public, $private, $subject ) {

    $point = padCryptUnB64 ( rtrim ( (string) $public, '=' ) );
    $d     = padCryptUnB64 ( rtrim ( (string) $private, '=' ) );

    if ( $point === NULL or $d === NULL or strlen ( $point ) != 65 or strlen ( $d ) != 32 )
      return '';

    $parts = parse_url ( $endpoint );
    $aud   = $parts ['scheme'] . '://' . $parts ['host'] . ( isset ( $parts ['port'] ) ? ':' . $parts ['port'] : '' );
    $jwt   = padJwtSign ( [ 'typ' => 'JWT', 'alg' => 'ES256' ], [ 'aud' => $aud, 'exp' => time () + 43200, 'sub' => (string) $subject ], padEcPem ( $d, $point ) );

    return ( $jwt === '' ) ? '' : 'vapid t=' . $jwt . ', k=' . padCryptB64 ( $point );

  }

?>
