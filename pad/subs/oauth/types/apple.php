<?php

  // apple - Sign in with Apple: a Services ID as APPLE_CLIENT_ID, and in place of a fixed
  // secret a key of the developer account - APPLE_TEAM_ID, APPLE_KEY_ID and the key itself
  // in APPLE_PRIVATE_KEY (the PEM text of the .p8 file, or its path) - from which every token
  // exchange makes the client secret Apple asks for: an ES256 JWT, signed with openssl. The
  // user is the id_token's claims - Apple has no userinfo - and the name comes once, with the
  // first login, in the user field Apple posts back; later logins have none. Apple posts the
  // answer (response_mode form_post) from its own site, which brings no SameSite=Lax session
  // cookie: the state goes in a sealed cookie of its own as well (__Host-padOAuth, SameSite=None,
  // https only), spent once used.

  return [
    'name'      => 'Apple',
    'icon'      => 'apple',
    'authorize' => 'https://appleid.apple.com/auth/authorize',
    'token'     => 'https://appleid.apple.com/auth/token',
    'userinfo'  => '',
    'issuer'    => 'https://appleid.apple.com',
    'oidc'      => TRUE,
    'scopes'    => 'name email',
    'pkce'      => FALSE,
    'post'      => TRUE,
    'params'    => [ 'response_mode' => 'form_post' ],
    'needs'     => [ 'client_id'   => 'APPLE_CLIENT_ID', 'team_id'     => 'APPLE_TEAM_ID',
                     'key_id'      => 'APPLE_KEY_ID',    'private_key' => 'APPLE_PRIVATE_KEY' ],
    'secret'    => function ( $settings ) {
      include_once PAD . 'subs/oauth/_lib/jwt.php';
      $pem = (string) $settings ['private_key'];
      if ( ! str_contains ( $pem, '-----BEGIN' ) and is_file ( $pem ) )
        $pem = (string) file_get_contents ( $pem );
      if ( ! str_contains ( $pem, "\n" ) )
        $pem = str_replace ( '\n', "\n", $pem );
      return padJwtSign ( [ 'alg' => 'ES256', 'kid' => (string) $settings ['key_id'] ],
                          [ 'iss' => (string) $settings ['team_id'], 'iat' => time (), 'exp' => time () + 3600,
                            'aud' => 'https://appleid.apple.com', 'sub' => (string) $settings ['client_id'] ], $pem );
    },
    'user'      => [ 'id' => 'sub', 'name' => '', 'email' => 'email', 'verified' => 'email_verified', 'avatar' => '', 'username' => '' ],
    'more'      => function ( $user, $answer, $get, $callback ) {
      $first = json_decode ( (string) ( $callback ['user'] ?? '' ), TRUE );
      $name  = is_array ( $first ['name'] ?? NULL ) ? trim ( ( $first ['name'] ['firstName'] ?? '' ) . ' ' . ( $first ['name'] ['lastName'] ?? '' ) ) : '';
      return [ 'name' => $name ] + $user;
    },
  ];

?>
