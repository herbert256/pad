<?php

  // google - Sign in with Google, OpenID Connect: a client of the Google Cloud console, its
  // id and secret in GOOGLE_CLIENT_ID and GOOGLE_CLIENT_SECRET. The id_token's issuer - either
  // of the two Google documents, https://accounts.google.com and accounts.google.com -,
  // audience, expiry and nonce are checked, the user is the userinfo answer (scopes openid,
  // email and profile).

  return [
    'name'      => 'Google',
    'icon'      => 'google',
    'authorize' => 'https://accounts.google.com/o/oauth2/v2/auth',
    'token'     => 'https://oauth2.googleapis.com/token',
    'userinfo'  => 'https://openidconnect.googleapis.com/v1/userinfo',
    'issuer'    => [ 'https://accounts.google.com', 'accounts.google.com' ],
    'oidc'      => TRUE,
    'scopes'    => 'openid email profile',
    'pkce'      => TRUE,
    'needs'     => [ 'client_id' => 'GOOGLE_CLIENT_ID', 'client_secret' => 'GOOGLE_CLIENT_SECRET' ],
    'user'      => [ 'id' => 'sub', 'name' => 'name|given_name', 'email' => 'email', 'verified' => 'email_verified', 'avatar' => 'picture', 'username' => '' ],
  ];

?>
