<?php

  // $padCurlLast keeps a copy of the last fetch, and the dumps and the JSON error report list
  // it - but the token endpoint's answer is a credential: access, refresh and id token, kept
  // for padOAuthToken in a static alone. The fetches of a login leave $padCurlLast as they
  // found it, also when the token exchange is the login's last request - every Apple login,
  // an id_token that fails its checks.

  include_once PAD . 'subs/oauth/_lib/jwt.php';

  $report = [];

  $look = function ( $label ) use ( &$report ) {
    $last = $GLOBALS ['padCurlLast'] ?? NULL;
    $report [$label] = [ 'login'      => padOAuthError () ?: 'logged in',
                         'last fetch' => is_array ( $last ) ? ( str_contains ( (string) ( $last ['data'] ?? '' ), 'ACCESS-SECRET' ) ? 'the token answer' : $last ['url'] )
                                                            : 'as it was' ];
  };

  $token = fn ( $iss, $aud, $nonce ) => [ 'data' => [ 'access_token' => 'ACCESS-SECRET', 'refresh_token' => 'REFRESH-SECRET', 'token_type' => 'Bearer',
    'id_token' => 'e30.' . padCryptB64 ( json_encode ( [ 'iss' => $iss, 'aud' => $aud, 'exp' => time () + 300, 'sub' => '1', 'nonce' => $nonce ] ) ) . '.c2ln' ] ];

  // Google, its id_token for another client: refused right after the exchange.

  $options = [ 'client_id' => 'shop', 'client_secret' => 'cs', 'redirect' => 'https://shop.example/oauth/google' ];

  parse_str ( parse_url ( padOAuthUrl ( 'google', $options ), PHP_URL_QUERY ), $asked );

  padCurlFake ( [ 'https://oauth2.googleapis.com/token' => $token ( 'https://accounts.google.com', 'another-client', $asked ['nonce'] ) ] );

  unset ( $GLOBALS ['padCurlLast'] );

  $_GET = [ 'code' => 'c', 'state' => $asked ['state'] ];

  padOAuthUser ( 'google', $options );

  $look ( 'google, the id_token refused' );

  // Apple: the user is the id_token's, the exchange is the last request.

  [ $d, $point ] = padEcNew ();

  $options = [ 'client_id' => 'com.example.web', 'team_id' => 'TEAM123456', 'key_id' => 'KEY1234567',
               'private_key' => padEcPem ( $d, $point ), 'redirect' => 'https://shop.example/oauth/apple' ];

  parse_str ( parse_url ( padOAuthUrl ( 'apple', $options ), PHP_URL_QUERY ), $asked );

  padCurlFake ( [ 'https://appleid.apple.com/auth/token' => $token ( 'https://appleid.apple.com', 'com.example.web', $asked ['nonce'] ) ] );

  unset ( $GLOBALS ['padCurlLast'] );

  $_GET = [ 'code' => 'c', 'state' => $asked ['state'] ];

  padOAuthUser ( 'apple', $options );

  $look ( 'apple' );

  $report ['padOAuthToken'] = array_keys ( padOAuthToken () );

  $json = json_encode ( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );

?>
