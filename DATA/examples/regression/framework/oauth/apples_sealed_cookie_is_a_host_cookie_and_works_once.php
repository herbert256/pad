<?php

  // Apple posts its answer back from its own site, so the login's record also goes into a
  // sealed cookie. That cookie is __Host-padOAuth - Secure, path /, no Domain - which a
  // sibling subdomain or a page over plain http cannot set, and its state works once: the same
  // cookie and state posted again - with an attacker's own code, in a browser the cookie was
  // planted in - is refused, as the session's record is once taken.

  include_once PAD . 'subs/oauth/_lib/jwt.php';

  [ $d, $point ] = padEcNew ();

  $options = [ 'client_id' => 'com.example.web', 'team_id' => 'TEAM123456', 'key_id' => 'KEY1234567',
               'private_key' => padEcPem ( $d, $point ), 'redirect' => 'https://shop.example/oauth/apple' ];

  parse_str ( parse_url ( padOAuthUrl ( 'apple', $options ), PHP_URL_QUERY ), $asked );

  $cookie = [ 'name' => '', 'attributes' => '' ];
  $sealed = '';

  foreach ( headers_list () as $header )
    if ( $sealed === '' and preg_match ( '/^Set-Cookie: ((?:__Host-)?padOAuth)=([^;]+)(;.*)$/i', $header, $match ) ) {
      [ , $cookie ['name'], $sealed, $rest ] = $match;
      $cookie ['attributes'] = preg_replace ( '/; expires=[^;]*; Max-Age=\d+/i', '', $rest );
    }

  unset ( $_SESSION ['padOAuth'] );

  padCurlFake ( [ 'https://appleid.apple.com/auth/token' => function () use ( $asked ) {
    $claims = [ 'iss' => 'https://appleid.apple.com', 'aud' => 'com.example.web', 'exp' => time () + 600, 'sub' => '001234.attacker', 'nonce' => $asked ['nonce'] ];
    return [ 'data' => [ 'access_token' => 'a', 'id_token' => 'e30.' . padCryptB64 ( json_encode ( $claims ) ) . '.c2ln' ] ];
  } ] );

  $posts = [];

  foreach ( [ 'the first post', 'the same cookie and state again', 'and a third time' ] as $i => $try ) {
    $_COOKIE = [ $cookie ['name'] => $sealed ];
    $_POST   = [ 'code' => "code-$i", 'state' => $asked ['state'] ];
    $user    = padOAuthUser ( 'apple', $options );
    $posts [$try] = $user ? $user ['id'] : padOAuthError ();
  }

  $json = json_encode ( [ 'cookie' => $cookie, 'posts' => $posts ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );

?>
