<?php

  // Sign In with LinkedIn using OpenID Connect as its documentation has it: the scopes openid,
  // profile and email, no PKCE and no nonce in the address (neither is a parameter of
  // LinkedIn's authorization), the code exchanged with the id and secret in the body, the
  // id_token's issuer https://www.linkedin.com (its documentation) or
  // https://www.linkedin.com/oauth (its discovery document) - any other refused - and the user
  // the userinfo answer, its address verified as email_verified says.

  $options = [ 'client_id' => '86xm2bnt4abcde', 'client_secret' => 'li-secret', 'redirect' => 'https://shop.example/oauth/linkedin' ];
  $iss     = '';
  $info    = [];
  $rows    = [];
  $first   = NULL;

  padCurlFake ( [
    'https://www.linkedin.com/oauth/v2/accessToken' => function () use ( &$iss ) {
      $claims = [ 'iss' => $iss, 'aud' => '86xm2bnt4abcde', 'iat' => time (), 'exp' => time () + 3600, 'sub' => '782bbtaQ' ];
      return [ 'data' => [ 'access_token' => 'AQUvlL_DYEzvT2wz1QJiEPeLioeA', 'expires_in' => 5184000, 'scope' => 'email,openid,profile',
                           'token_type' => 'Bearer', 'id_token' => 'e30.' . padCryptB64 ( json_encode ( $claims ) ) . '.c2ln' ] ];
    },
    'https://api.linkedin.com/v2/userinfo' => function () use ( &$info ) { return [ 'data' => $info ]; },
  ] );

  $member = [ 'sub' => '782bbtaQ', 'name' => 'John Doe', 'given_name' => 'John', 'family_name' => 'Doe',
              'picture' => 'https://media.licdn-ei.com/dms/image/C5F03AQHqK8v7tB1HCQ/profile-displayphoto-shrink_100_100/0/', 'locale' => 'en-US' ];

  $logins = [ 'documented issuer'          => [ 'https://www.linkedin.com',       $member + [ 'email' => 'doe@email.com', 'email_verified' => TRUE ] ],
              'issuer of the discovery'    => [ 'https://www.linkedin.com/oauth', $member + [ 'email' => 'doe@email.com', 'email_verified' => TRUE ] ],
              'address not verified'       => [ 'https://www.linkedin.com',       $member + [ 'email' => 'doe@email.com', 'email_verified' => FALSE ] ],
              'no address at all'          => [ 'https://www.linkedin.com',       $member ],
              'another issuer'             => [ 'https://evil.example.com',       $member ] ];

  foreach ( $logins as $label => [ $iss, $info ] ) {

    $start = padOAuthUrl ( 'linkedin', $options );

    parse_str ( parse_url ( $start, PHP_URL_QUERY ), $asked );

    $_GET  = [ 'code' => 'AQTQmah11lalyH65DAIivsjsAQV5P', 'state' => $asked ['state'] ];
    $user  = padOAuthUser ( 'linkedin', $options );
    $first = $first ?? $user;

    $rows [$label] = $user ? $user ['id'] . " '" . $user ['email'] . "' " . var_export ( $user ['email_verified'], TRUE ) : padOAuthError ();

  }

  $calls = padCurlRecorded ();

  parse_str ( $calls [0] ['post'], $exchanged );

  $json = json_encode ( [
    'authorize' => strtok ( $start, '?' ),
    'asked'     => array_keys ( $asked ),
    'scope'     => $asked ['scope'],
    'exchange'  => $calls [0] ['method'] . ' ' . $calls [0] ['url'],
    'basic'     => $calls [0] ['headers'] ['Authorization'] ?? '(none)',
    'body'      => array_keys ( $exchanged ),
    'user'      => $first,
    'logins'    => $rows,
  ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );

?>
