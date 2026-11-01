<?php

  // linkedin - Sign In with LinkedIn using OpenID Connect: an app of the developer portal with
  // that product added, its client id and secret in LINKEDIN_CLIENT_ID and
  // LINKEDIN_CLIENT_SECRET. As learn.microsoft.com/linkedin has it (Sign In with LinkedIn using
  // OpenID Connect; Authorization Code Flow): the scopes openid, profile and email, the code
  // exchanged with the id and secret in the body, no PKCE (LinkedIn has it for native apps
  // alone) and no nonce - neither is a parameter of its authorization - the user the userinfo
  // answer, its email verified when its email_verified says so. The id_token's issuer is
  // https://www.linkedin.com in the documentation, https://www.linkedin.com/oauth in the
  // discovery document at www.linkedin.com/oauth/.well-known/openid-configuration - either is
  // taken, as either is LinkedIn's - which is why the generic oidc cannot log in with LinkedIn:
  // it holds the token to the document's issuer, and the document names no
  // client_secret_post, which LinkedIn's token endpoint is documented to take.

  return [
    'name'      => 'LinkedIn',
    'icon'      => 'linkedin',
    'authorize' => 'https://www.linkedin.com/oauth/v2/authorization',
    'token'     => 'https://www.linkedin.com/oauth/v2/accessToken',
    'userinfo'  => 'https://api.linkedin.com/v2/userinfo',
    'issuer'    => [ 'https://www.linkedin.com', 'https://www.linkedin.com/oauth' ],
    'oidc'      => TRUE,
    'nonce'     => FALSE,
    'scopes'    => 'openid profile email',
    'pkce'      => FALSE,
    'needs'     => [ 'client_id' => 'LINKEDIN_CLIENT_ID', 'client_secret' => 'LINKEDIN_CLIENT_SECRET' ],
    'user'      => [ 'id' => 'sub', 'name' => 'name|given_name', 'email' => 'email', 'verified' => 'email_verified', 'avatar' => 'picture', 'username' => '' ],
  ];

?>
