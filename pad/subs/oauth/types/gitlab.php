<?php

  // gitlab - Log in with GitLab: an application of gitlab.com, or of a GitLab of your own
  // with its address in the option base or GITLAB_URL; its id and secret in
  // GITLAB_CLIENT_ID and GITLAB_CLIENT_SECRET. The user is GET /api/v4/user (scope
  // read_user), the address verified when its confirmed_at says when.

  $base = rtrim ( (string) ( $options ['base'] ?? padEnv ( 'GITLAB_URL', 'https://gitlab.com' ) ), '/' );

  // A GitLab of one's own counts its users as gitlab.com does: its address goes into the id.

  return [
    'name'      => 'GitLab',
    'instance'  => ( strtolower ( $base ) === 'https://gitlab.com' ) ? '' : $base,
    'icon'      => 'gitlab',
    'authorize' => "$base/oauth/authorize",
    'token'     => "$base/oauth/token",
    'userinfo'  => "$base/api/v4/user",
    'scopes'    => 'read_user',
    'pkce'      => TRUE,
    'needs'     => [ 'client_id' => 'GITLAB_CLIENT_ID', 'client_secret' => 'GITLAB_CLIENT_SECRET' ],
    'user'      => [ 'id' => 'id', 'name' => 'name|username', 'email' => 'email', 'avatar' => 'avatar_url', 'username' => 'username' ],
    'more'      => fn ( $user, $answer ) => [ 'email_verified' => is_string ( $answer ['confirmed_at'] ?? NULL ) and trim ( $answer ['confirmed_at'] ) !== '' ] + $user,
  ];

?>
