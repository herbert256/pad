<?php

  // The generic OpenID Connect provider, with another prefix for its settings (env=KEYCLOAK,
  // so KEYCLOAK_ISSUER and the like): its endpoints from the issuer's discovery document,
  // whose token endpoint takes client_secret_basic only - the client authenticates with HTTP
  // Basic then, not in the body. A document naming another issuer is refused.

  $options = [ 'env' => 'KEYCLOAK', 'issuer' => 'https://id.example.com/realms/main', 'client_id' => 'shop', 'client_secret' => 's3cret',
               'redirect' => 'https://shop.example/oauth/oidc', 'name' => 'Example ID' ];

  $nonce = '';

  padCurlFake ( [
    'https://id.example.com/realms/main/.well-known/openid-configuration' => [ 'data' => [
      'issuer'                                => 'https://id.example.com/realms/main',
      'authorization_endpoint'                => 'https://id.example.com/auth',
      'token_endpoint'                        => 'https://id.example.com/token',
      'userinfo_endpoint'                     => 'https://id.example.com/userinfo',
      'token_endpoint_auth_methods_supported' => [ 'client_secret_basic', 'private_key_jwt' ] ] ],
    'https://id.example.com/token'    => function () use ( &$nonce ) {
      $claims = [ 'iss' => 'https://id.example.com/realms/main', 'aud' => 'shop', 'exp' => time () + 300, 'sub' => 'f81d4fae', 'nonce' => $nonce ];
      return [ 'data' => [ 'access_token' => 'at', 'id_token' => 'e30.' . padCryptB64 ( json_encode ( $claims ) ) . '.c2ln' ] ];
    },
    'https://id.example.com/userinfo' => [ 'data' => [ 'sub' => 'f81d4fae', 'preferred_username' => 'jdoe', 'email' => 'jdoe@example.com' ] ],
    'https://evil.example.com/.well-known/openid-configuration' => [ 'data' => [ 'issuer' => 'https://id.example.com/realms/main' ] ],
  ] );

  parse_str ( parse_url ( padOAuthUrl ( 'oidc', $options ), PHP_URL_QUERY ), $asked );

  $nonce = $asked ['nonce'];
  $_GET  = [ 'code' => 'c', 'state' => $asked ['state'] ];
  $user  = padOAuthUser ( 'oidc', $options );
  $token = array_values ( array_filter ( padCurlRecorded (), fn ( $call ) => $call ['url'] == 'https://id.example.com/token' ) ) [0];

  parse_str ( $token ['post'], $body );

  $other = padOAuthUrl ( 'oidc', [ 'issuer' => 'https://evil.example.com' ] + $options );

  $json = json_encode ( [ 'token' => $token ['url'], 'basic' => $token ['headers'] ['Authorization'] ?? '(none)', 'body' => array_keys ( $body ),
                          'user' => $user, 'other issuer' => [ $other, padOAuthError () ] ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );

?>
