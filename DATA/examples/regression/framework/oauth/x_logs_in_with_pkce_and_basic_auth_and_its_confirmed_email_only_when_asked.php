<?php

  // Log in with X as docs.x.com has it: the authorize page x.com/i/oauth2/authorize with the
  // PKCE S256 challenge X requires and the scopes tweet.read and users.read that /2/users/me
  // needs, the code exchanged at api.x.com/2/oauth2/token with the id and secret as HTTP
  // Basic and the verifier, the user the data of GET /2/users/me with its profile image. An
  // address comes only with the scope users.email: then confirmed_email is asked for too, and
  // counts as verified. A refused token - X's problem answer, 401 - logs no one in.

  $options = [ 'client_id' => 'M1M5R3BMVy13QmpScXkzTUt5OE46MTpjaQ', 'client_secret' => 'x-secret', 'redirect' => 'https://shop.example/oauth/x' ];
  $refuse  = FALSE;
  $rows    = [];
  $first   = NULL;

  padCurlFake ( [
    'https://api.x.com/2/oauth2/token' => [ 'data' => [ 'token_type' => 'bearer', 'expires_in' => 7200, 'access_token' => 'x-access', 'scope' => 'users.read tweet.read' ] ],
    'https://api.x.com/2/users/me?*'   => function ( $request ) use ( &$refuse ) {
      if ( $refuse )
        return [ 'status' => 401, 'data' => [ 'title' => 'Unauthorized', 'type' => 'about:blank', 'status' => 401, 'detail' => 'Unauthorized' ] ];
      $user = [ 'id' => '2244994945', 'name' => 'X Dev', 'username' => 'XDevelopers',
                'profile_image_url' => 'https://pbs.twimg.com/profile_images/1683325380441128960/yRsRRjGO_normal.jpg' ];
      if ( str_contains ( $request ['url'], 'confirmed_email' ) )
        $user ['confirmed_email'] = 'dev@example.com';
      return [ 'data' => [ 'data' => $user ] ];
    },
  ] );

  $logins = [ 'the default scopes'   => [],
              'with users.email'     => [ 'scopes' => 'tweet.read users.read users.email' ],
              'a refused token'      => [] ];

  foreach ( $logins as $label => $more ) {

    $refuse = ( $label == 'a refused token' );
    $start  = padOAuthUrl ( 'x', $more + $options );

    parse_str ( parse_url ( $start, PHP_URL_QUERY ), $asked );

    $_GET  = [ 'code' => 'VGNibzFWSWREZm01bjN1N3dicWlNUG1oa2xRRVNNdmVHelJGY2hPWGxNd2dxOjE2MjIxNjA4MjU4MjU6MToxOmFjOjE', 'state' => $asked ['state'] ];
    $user  = padOAuthUser ( 'x', $more + $options );
    $first = $first ?? [ 'asked' => $asked, 'user' => $user ];

    $rows [$label] = '[' . $asked ['scope'] . '] ' . ( $user ? $user ['id'] . " '" . $user ['email'] . "' " . var_export ( $user ['email_verified'], TRUE ) : padOAuthError () );

  }

  $calls = padCurlRecorded ();

  parse_str ( $calls [0] ['post'], $exchanged );

  $json = json_encode ( [
    'authorize' => strtok ( $start, '?' ),
    'asked'     => array_keys ( $first ['asked'] ),
    'method'    => $first ['asked'] ['code_challenge_method'],
    'challenge' => $first ['asked'] ['code_challenge'] === padCryptB64 ( hash ( 'sha256', $exchanged ['code_verifier'], TRUE ) ),
    'exchange'  => $calls [0] ['method'] . ' ' . $calls [0] ['url'],
    'basic'     => $calls [0] ['headers'] ['Authorization'] === 'Basic ' . base64_encode ( $options ['client_id'] . ':' . $options ['client_secret'] ),
    'body'      => array_keys ( $exchanged ),
    'fetched'   => array_values ( array_unique ( array_map ( fn ( $call ) => $call ['url'], array_filter ( $calls, fn ( $call ) => $call ['method'] == 'GET' ) ) ) ),
    'user'      => $first ['user'],
    'logins'    => $rows,
  ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );

?>
