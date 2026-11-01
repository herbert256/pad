<?php

  // Sign in with Apple: the client secret is an ES256 JWT made with the developer key for
  // every exchange - here a key of the test's own, so the token endpoint (a fake) verifies
  // the signature with its public half. Apple posts its answer back from its own site, which
  // brings no SameSite=Lax session cookie: the session's record is gone here, and the login
  // is found through the sealed padOAuth cookie the start set. The name comes once, in the
  // posted user field; the rest is the id_token's.

  include_once PAD . 'subs/oauth/_lib/jwt.php';

  [ $d, $point ] = padEcNew ();

  $options = [ 'client_id' => 'com.example.web', 'team_id' => 'TEAM123456', 'key_id' => 'KEY1234567',
               'private_key' => padEcPem ( $d, $point ), 'redirect' => 'https://shop.example/oauth/apple' ];

  parse_str ( parse_url ( padOAuthUrl ( 'apple', $options ), PHP_URL_QUERY ), $asked );

  $sealed = '';

  foreach ( headers_list () as $header )
    if ( preg_match ( '/^Set-Cookie: padOAuth=([^;]+);.*SameSite=None/i', $header, $match ) and preg_match ( '/; secure/i', $header ) )
      $sealed = $match [1];

  unset ( $_SESSION ['padOAuth'] );

  $secret = [];

  padCurlFake ( [ 'https://appleid.apple.com/auth/token' => function ( $request ) use ( $point, $asked, &$secret ) {
    parse_str ( $request ['post'], $body );
    $secret = [ 'verified' => padJwtVerify ( $body ['client_secret'], padEcPublicPem ( $point ) ),
                'header'   => json_decode ( padCryptUnB64 ( strtok ( $body ['client_secret'], '.' ) ), TRUE ),
                'claims'   => array_intersect_key ( padJwtClaims ( $body ['client_secret'] ), array_flip ( [ 'iss', 'aud', 'sub' ] ) ) ];
    $claims = [ 'iss' => 'https://appleid.apple.com', 'aud' => 'com.example.web', 'exp' => time () + 600, 'sub' => '001234.abcd.0001',
                'email' => 'x7q2@privaterelay.appleid.com', 'nonce' => $asked ['nonce'] ];
    return [ 'data' => [ 'access_token' => 'a1', 'token_type' => 'Bearer', 'id_token' => 'e30.' . padCryptB64 ( json_encode ( $claims ) ) . '.c2ln' ] ];
  } ] );

  $_COOKIE ['padOAuth'] = $sealed;
  $_POST = [ 'code' => 'c1', 'state' => $asked ['state'], 'user' => '{"name":{"firstName":"Ann","lastName":"Smith"},"email":"x7q2@privaterelay.appleid.com"}' ];

  $user = padOAuthUser ( 'apple', $options );

  $json = json_encode ( [ 'cookie' => $sealed !== '', 'secret' => $secret, 'user' => $user, 'error' => padOAuthError () ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );

?>
