<?php

  // oidc - any OpenID Connect provider - Keycloak, Authentik, Zitadel, Auth0, Okta, a
  // company's own: its issuer in the option issuer or OIDC_ISSUER, its client in
  // OIDC_CLIENT_ID and OIDC_CLIENT_SECRET (or another prefix with the option env='KEYCLOAK'
  // - KEYCLOAK_ISSUER, KEYCLOAK_CLIENT_ID ...). The endpoints come from the issuer's
  // /.well-known/openid-configuration, read once a day; the issuer it names must be the one
  // given. The id_token is checked, the user is the userinfo answer.

  $env = strtoupper ( (string) ( $options ['env'] ?? 'OIDC' ) );
  $env = preg_match ( '/^[A-Z][A-Z0-9_]*$/D', $env ) ? $env : 'OIDC';

  return [
    'name'      => (string) ( $options ['name'] ?? 'OpenID' ),
    'icon'      => 'key',
    'discovery' => TRUE,
    'oidc'      => TRUE,
    'scopes'    => 'openid email profile',
    'pkce'      => TRUE,
    'needs'     => [ 'issuer' => "{$env}_ISSUER", 'client_id' => "{$env}_CLIENT_ID", 'client_secret' => "{$env}_CLIENT_SECRET" ],
    'user'      => [ 'id' => 'sub', 'name' => 'name|preferred_username', 'email' => 'email', 'verified' => 'email_verified', 'avatar' => 'picture',
                     'username' => 'preferred_username' ],
  ];

?>
