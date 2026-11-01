<?php

  // microsoft - Sign in with Microsoft, OpenID Connect on the identity platform: an app
  // registration's id and secret in MICROSOFT_CLIENT_ID and MICROSOFT_CLIENT_SECRET, the
  // tenant in the option tenant or MICROSOFT_TENANT - common (default: work, school and
  // personal accounts), organizations, consumers, or a tenant's own id or domain. The
  // id_token's issuer names the tenant of the account, so it is checked against
  // login.microsoftonline.com/<its tid>/v2.0 - and against the tenant given, when that is an
  // id. The user is Graph's OIDC userinfo with the id_token's claims under it. Its email is
  // the email claim alone, which Microsoft does not verify - preferred_username, which any
  // tenant can set to any address, is the username - and it counts as verified only with the
  // optional claim xms_edov (the domain's owner verified) set up for the app.

  $tenant = trim ( (string) ( $options ['tenant'] ?? padEnv ( 'MICROSOFT_TENANT', 'common' ) ) );
  $tenant = preg_match ( '/^[A-Za-z0-9.-]+$/D', $tenant ) ? $tenant : 'common';
  $guid   = preg_match ( '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/iD', $tenant );

  return [
    'name'      => 'Microsoft',
    'icon'      => 'microsoft',
    'authorize' => "https://login.microsoftonline.com/$tenant/oauth2/v2.0/authorize",
    'token'     => "https://login.microsoftonline.com/$tenant/oauth2/v2.0/token",
    'userinfo'  => 'https://graph.microsoft.com/oidc/userinfo',
    'issuer'    => 'https://login.microsoftonline.com/{tid}/v2.0',
    'claims'    => $guid ? [ 'tid' => strtolower ( $tenant ) ] : [],
    'oidc'      => TRUE,
    'scopes'    => 'openid email profile',
    'pkce'      => TRUE,
    'needs'     => [ 'client_id' => 'MICROSOFT_CLIENT_ID', 'client_secret' => 'MICROSOFT_CLIENT_SECRET' ],
    'user'      => [ 'id' => 'sub', 'name' => 'name|preferred_username', 'email' => 'email', 'verified' => 'email_verified|xms_edov', 'avatar' => '',
                     'username' => 'preferred_username' ],
  ];

?>
