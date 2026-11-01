<?php

  // Google documents two values of its id_token's iss - https://accounts.google.com and
  // accounts.google.com - and a token with either is Google's: a provider's issuer may be a
  // list of the values it uses. Any other issuer is still refused.

  $options = [ 'client_id' => 'web.apps.googleusercontent.com', 'client_secret' => 'cs', 'redirect' => 'https://shop.example/oauth/google' ];
  $results = [];

  foreach ( [ 'https://accounts.google.com', 'accounts.google.com', 'https://accounts.google.com.evil.example' ] as $iss ) {

    padCurlFakeStop ();

    parse_str ( parse_url ( padOAuthUrl ( 'google', $options ), PHP_URL_QUERY ), $asked );

    $claims = [ 'iss' => $iss, 'aud' => 'web.apps.googleusercontent.com', 'sub' => '1093', 'exp' => time () + 3600, 'nonce' => $asked ['nonce'] ];

    padCurlFake ( [
      'https://oauth2.googleapis.com/token' => [ 'data' => [ 'access_token' => 'ya29', 'id_token' => 'e30.' . padCryptB64 ( json_encode ( $claims ) ) . '.c2ln' ] ],
      'https://openidconnect.googleapis.com/v1/userinfo' => [ 'data' => [ 'sub' => '1093', 'name' => 'Ann Smith' ] ] ] );

    $_GET = [ 'code' => 'c', 'state' => $asked ['state'] ];

    $user = padOAuthUser ( 'google', $options );

    $results [$iss] = $user ? $user ['id'] : padOAuthError ();

  }

  $json = json_encode ( $results, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );

?>
