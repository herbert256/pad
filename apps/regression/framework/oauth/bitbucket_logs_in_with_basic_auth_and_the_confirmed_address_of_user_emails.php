<?php

  // Bitbucket Cloud as its OAuth 2.0 documentation has it: the authorize address without
  // scopes (a consumer's permissions are set at Bitbucket) and without PKCE, the code
  // exchanged with the consumer's key and secret as HTTP Basic - not in the body - the user
  // GET /2.0/user, its uuid the id, and the address from GET /2.0/user/emails: the primary
  // one when it is confirmed, else the first confirmed one, never an unconfirmed one.

  $options = [ 'client_id' => 'bbkey', 'client_secret' => 'bbsecret', 'redirect' => 'https://shop.example/oauth/bitbucket' ];
  $emails  = [];
  $rows    = [];
  $first   = NULL;

  padCurlFake ( [
    'https://bitbucket.org/site/oauth2/access_token' => [ 'data' => [ 'access_token' => 'bb-token', 'scopes' => 'account email', 'expires_in' => 7200,
                                                                      'refresh_token' => 'bb-refresh', 'token_type' => 'bearer' ] ],
    'https://api.bitbucket.org/2.0/user'             => [ 'data' => [ 'type' => 'user', 'uuid' => '{c788b2da-b7a2-404c-9e26-d3f077557007}',
                                                                      'account_id' => '557058:c0b72ad0-1cb5-4018-9cdc-0cde8492c443',
                                                                      'display_name' => 'Ann Smith', 'nickname' => 'annsmith',
                                                                      'links' => [ 'avatar' => [ 'href' => 'https://avatar-management.example/ann.png' ] ] ] ],
    'https://api.bitbucket.org/2.0/user/emails'      => function () use ( &$emails ) {
      return [ 'data' => [ 'pagelen' => 10, 'values' => $emails, 'page' => 1, 'size' => count ( $emails ) ] ];
    },
  ] );

  $address = fn ( $email, $primary, $confirmed ) => [ 'is_primary' => $primary, 'is_confirmed' => $confirmed, 'type' => 'email', 'email' => $email ];

  $lists = [ 'primary confirmed'   => [ $address ( 'old@example.com', FALSE, TRUE ), $address ( 'ann@example.com', TRUE, TRUE ) ],
             'primary unconfirmed' => [ $address ( 'new@example.com', TRUE, FALSE ), $address ( 'ann@example.com', FALSE, TRUE ) ],
             'nothing confirmed'   => [ $address ( 'new@example.com', TRUE, FALSE ) ] ];

  foreach ( $lists as $label => $emails ) {

    $start = padOAuthUrl ( 'bitbucket', $options );

    parse_str ( parse_url ( $start, PHP_URL_QUERY ), $asked );

    $_GET  = [ 'code' => 'bb-code', 'state' => $asked ['state'] ];
    $user  = padOAuthUser ( 'bitbucket', $options );
    $first = $first ?? $user;

    $rows [$label] = $user ? "'" . $user ['email'] . "' " . var_export ( $user ['email_verified'], TRUE ) : padOAuthError ();

  }

  $calls = padCurlRecorded ();

  parse_str ( $calls [0] ['post'], $exchanged );

  $json = json_encode ( [
    'authorize' => strtok ( $start, '?' ),
    'asked'     => array_keys ( $asked ),
    'exchange'  => $calls [0] ['method'] . ' ' . $calls [0] ['url'],
    'basic'     => $calls [0] ['headers'] ['Authorization'] ?? '(none)',
    'body'      => array_keys ( $exchanged ),
    'bearer'    => $calls [1] ['headers'] ['Authorization'],
    'fetched'   => array_map ( fn ( $call ) => $call ['url'], array_slice ( $calls, 1, 2 ) ),
    'user'      => $first,
    'addresses' => $rows,
  ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );

?>
