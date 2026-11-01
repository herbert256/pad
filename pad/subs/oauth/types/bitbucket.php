<?php

  // bitbucket - Log in with Bitbucket (Bitbucket Cloud): an OAuth consumer of a workspace
  // (Settings, OAuth consumers), its key and secret in BITBUCKET_CLIENT_ID and
  // BITBUCKET_CLIENT_SECRET, its callback URL the address of the login page, and its
  // permissions Account: Read and Email - a consumer's scopes are set there, not asked for in
  // the address. As developer.atlassian.com/cloud/bitbucket/rest/intro (OAuth 2.0) has it:
  // the authorize page bitbucket.org/site/oauth2/authorize, the code exchanged at
  // site/oauth2/access_token with the key and secret as HTTP Basic, no PKCE, and every API
  // call to api.bitbucket.org with the token as Bearer. The user is GET /2.0/user - its uuid
  // the id (the username is gone since 2019; nickname is the handle) - and the address comes
  // from GET /2.0/user/emails, which /2.0/user never holds: the primary one when it is
  // confirmed, else the first confirmed one - never an unconfirmed one.

  return [
    'name'      => 'Bitbucket',
    'icon'      => 'bitbucket',
    'authorize' => 'https://bitbucket.org/site/oauth2/authorize',
    'token'     => 'https://bitbucket.org/site/oauth2/access_token',
    'userinfo'  => 'https://api.bitbucket.org/2.0/user',
    'scopes'    => '',
    'pkce'      => FALSE,
    'auth'      => 'basic',
    'needs'     => [ 'client_id' => 'BITBUCKET_CLIENT_ID', 'client_secret' => 'BITBUCKET_CLIENT_SECRET' ],
    'user'      => [ 'id' => 'uuid', 'name' => 'display_name|nickname', 'email' => '', 'avatar' => 'links.avatar.href', 'username' => 'nickname' ],
    'more'      => function ( $user, $answer, $get ) {
      $emails = $get ( 'https://api.bitbucket.org/2.0/user/emails' );
      $emails = is_array ( $emails ['values'] ?? NULL ) ? $emails ['values'] : [];
      foreach ( [ TRUE, FALSE ] as $primary )
        foreach ( $emails as $one )
          if ( is_array ( $one ) and ( $one ['is_confirmed'] ?? NULL ) === TRUE and ( ! $primary or ( $one ['is_primary'] ?? NULL ) === TRUE )
               and is_string ( $one ['email'] ?? NULL ) and trim ( $one ['email'] ) !== '' )
            return [ 'email' => $one ['email'], 'email_verified' => TRUE ] + $user;
      return $user;
    },
  ];

?>
