<?php

  // forgejo - Log in with Forgejo (or Gitea): Codeberg by default, or the forge at the
  // address in the option base or FORGEJO_URL; an OAuth2 application's id and secret in
  // FORGEJO_CLIENT_ID and FORGEJO_CLIENT_SECRET. The user is GET /api/v1/user.

  $base = rtrim ( (string) ( $options ['base'] ?? padEnv ( 'FORGEJO_URL', 'https://codeberg.org' ) ), '/' );

  return [
    'name'      => ( $base === 'https://codeberg.org' ) ? 'Codeberg' : 'Forgejo',
    'icon'      => 'forgejo',
    'authorize' => "$base/login/oauth/authorize",
    'token'     => "$base/login/oauth/access_token",
    'userinfo'  => "$base/api/v1/user",
    'scopes'    => 'read:user',
    'pkce'      => TRUE,
    'needs'     => [ 'client_id' => 'FORGEJO_CLIENT_ID', 'client_secret' => 'FORGEJO_CLIENT_SECRET' ],
    'user'      => [ 'id' => 'id', 'name' => 'full_name|login', 'email' => 'email', 'avatar' => 'avatar_url', 'username' => 'login' ],
  ];

?>
