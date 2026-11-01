<?php

  // Microsoft's id_token names the tenant of the account in its issuer -
  // login.microsoftonline.com/<tid>/v2.0 - so with the tenant common any tenant's issuer
  // passes when it matches the token's own tid, and a tenant given by its id lets only that
  // tenant's accounts in. The username is the id_token's preferred_username, under Graph's
  // userinfo.

  $tid   = '9188040d-6c67-4c5b-b112-36a304b66dad';
  $nonce = '';
  $iss   = '';

  padCurlFake ( [
    'https://login.microsoftonline.com/*/oauth2/v2.0/token' => function () use ( &$nonce, &$iss, $tid ) {
      $claims = [ 'iss' => $iss, 'tid' => $tid, 'aud' => 'app-id', 'exp' => time () + 300, 'sub' => 'AAAAbbbb', 'nonce' => $nonce,
                  'preferred_username' => 'ann@contoso.com' ];
      return [ 'data' => [ 'access_token' => 'at', 'id_token' => 'e30.' . padCryptB64 ( json_encode ( $claims ) ) . '.c2ln' ] ];
    },
    'https://graph.microsoft.com/oidc/userinfo' => [ 'data' => [ 'sub' => 'AAAAbbbb', 'name' => 'Ann Smith', 'email' => 'ann@contoso.com' ] ],
  ] );

  $cases = [ 'common, its own tid'          => [ 'common', "https://login.microsoftonline.com/$tid/v2.0" ],
             'common, another tid in iss'   => [ 'common', 'https://login.microsoftonline.com/72f988bf-86f1-41af-91ab-2d7cd011db47/v2.0' ],
             'the tenant given by its id'   => [ $tid, "https://login.microsoftonline.com/$tid/v2.0" ],
             'another tenant given'         => [ '72f988bf-86f1-41af-91ab-2d7cd011db47', "https://login.microsoftonline.com/$tid/v2.0" ] ];

  $results = [];

  foreach ( $cases as $case => [ $tenant, $iss ] ) {

    $options = [ 'tenant' => $tenant, 'client_id' => 'app-id', 'client_secret' => 'cs', 'redirect' => 'https://shop.example/oauth/microsoft' ];

    parse_str ( parse_url ( padOAuthUrl ( 'microsoft', $options ), PHP_URL_QUERY ), $asked );

    $nonce = $asked ['nonce'];
    $_GET  = [ 'code' => 'c', 'state' => $asked ['state'] ];
    $user  = padOAuthUser ( 'microsoft', $options );

    $results [] = [ 'case' => $case, 'got' => $user ? $user ['id'] . ' ' . $user ['username'] : padOAuthError () ];

  }

?>
