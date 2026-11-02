<?php
  // Administrator-registered application on one configured Mastodon 4.3+ instance (PKCE S256).
  $base = $options ['base'] ?? padEnv ( 'MASTODON_URL', '' );
  $base = is_string ( $base ) ? rtrim ( $base, '/' ) : '';
  if ( ! preg_match ( '~^https://[A-Za-z0-9.-]+(?::[0-9]{1,5})?$~D', $base ) ) $base = '';
  $base = strtolower ( $base );
  return [
    'name' => 'Mastodon', 'instance' => $base, 'icon' => 'globe',
    'authorize' => "$base/oauth/authorize", 'token' => "$base/oauth/token",
    'userinfo' => "$base/api/v1/accounts/verify_credentials",
    'scopes' => 'read:accounts', 'pkce' => TRUE,
    'needs' => [ 'client_id' => 'MASTODON_CLIENT_ID', 'client_secret' => 'MASTODON_CLIENT_SECRET' ],
    'user' => [ 'id' => 'id', 'name' => 'display_name|username', 'username' => 'acct|username', 'avatar' => 'avatar_static|avatar' ],
    'more' => fn ( $user, $answer ) => [ 'email' => '', 'email_verified' => FALSE ] + $user,
  ];
?>
