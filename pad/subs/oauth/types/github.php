<?php

  // github - Log in with GitHub: an OAuth app (Settings, Developer settings), its client id
  // and secret in GITHUB_CLIENT_ID and GITHUB_CLIENT_SECRET. The user is GET /user; an
  // address the visitor keeps private answers null there, so it is taken from /user/emails -
  // the primary one when it is verified, else the first verified one (scope user:email).

  return [
    'name'      => 'GitHub',
    'icon'      => 'github',
    'authorize' => 'https://github.com/login/oauth/authorize',
    'token'     => 'https://github.com/login/oauth/access_token',
    'userinfo'  => 'https://api.github.com/user',
    'scopes'    => 'read:user user:email',
    'pkce'      => TRUE,
    'needs'     => [ 'client_id' => 'GITHUB_CLIENT_ID', 'client_secret' => 'GITHUB_CLIENT_SECRET' ],
    'headers'   => [ 'Accept' => 'application/vnd.github+json', 'X-GitHub-Api-Version' => '2022-11-28' ],
    'user'      => [ 'id' => 'id', 'name' => 'name|login', 'email' => 'email', 'avatar' => 'avatar_url', 'username' => 'login' ],
    'more'      => function ( $user, $answer, $get ) {
      if ( $user ['email'] !== '' )
        return $user;
      $emails = $get ( 'https://api.github.com/user/emails' );
      foreach ( [ TRUE, FALSE ] as $primary )
        foreach ( is_array ( $emails ) ? $emails : [] as $one )
          if ( is_array ( $one ) and ! empty ( $one ['verified'] ) and ( ! $primary or ! empty ( $one ['primary'] ) ) and is_string ( $one ['email'] ?? NULL ) )
            return [ 'email' => $one ['email'] ] + $user;
      return $user;
    },
  ];

?>
