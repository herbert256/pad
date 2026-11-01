<?php

  // Sign in with Google, OpenID Connect: the authorize address asks for a nonce, and the
  // id_token that comes with the tokens must name Google as its issuer, this client as its
  // audience, an expiry still ahead and that nonce - a login is refused for each one that is
  // wrong. Its signature is not checked: it comes straight from the token endpoint over TLS
  // (OpenID Connect Core 3.1.3.7, item 6). The userinfo's sub must be the token's.

  $options = [ 'client_id' => 'web.apps.googleusercontent.com', 'client_secret' => 'cs', 'redirect' => 'https://shop.example/oauth/google' ];

  function googleToken ( $claims ) {
    return padCryptB64 ( '{"alg":"RS256","kid":"k1"}' ) . '.' . padCryptB64 ( json_encode ( $claims ) ) . '.c2lnbmF0dXJl';
  }

  $cases = [
    'right'          => [],
    'other issuer'   => [ 'iss' => 'https://accounts.example.com' ],
    'other client'   => [ 'aud' => 'another.apps.googleusercontent.com' ],
    'two audiences'  => [ 'aud' => [ 'web.apps.googleusercontent.com', 'other' ] ],
    'expired'        => [ 'exp' => time () - 3600 ],
    'other nonce'    => [ 'nonce' => 'replayed' ],
    'other userinfo' => [ 'sub' => '999' ],
  ];

  $results = [];

  foreach ( $cases as $case => $change ) {

    padCurlFakeStop ();

    parse_str ( parse_url ( padOAuthUrl ( 'google', $options ), PHP_URL_QUERY ), $asked );

    $claims = $change + [ 'iss' => 'https://accounts.google.com', 'aud' => 'web.apps.googleusercontent.com', 'sub' => '1093', 'exp' => time () + 3600,
                          'iat' => time (), 'nonce' => $asked ['nonce'], 'email' => 'ann@example.com' ];

    padCurlFake ( [
      'https://oauth2.googleapis.com/token' => [ 'data' => [ 'access_token' => 'ya29', 'id_token' => googleToken ( $claims ), 'expires_in' => 3599 ] ],
      'https://openidconnect.googleapis.com/v1/userinfo' => [ 'data' => [ 'sub' => '1093', 'name' => 'Ann Smith', 'email' => 'ann@example.com', 'picture' => 'https://lh3.example/a.png' ] ],
    ] );

    $_GET = [ 'code' => 'c', 'state' => $asked ['state'] ];

    $user = padOAuthUser ( 'google', $options );

    $results [] = [ 'case' => $case, 'nonce' => isset ( $asked ['nonce'] ) ? 'asked' : 'not asked', 'scope' => $asked ['scope'],
                    'user' => $user ? $user ['id'] . ' ' . $user ['name'] . ' <' . $user ['email'] . '>' : '', 'error' => padOAuthError () ];

  }

?>
