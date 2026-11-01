<?php

  // Log in with GitHub, Google, Microsoft, Apple ... - the OAuth 2.0 authorization code flow
  // with PKCE, and OpenID Connect on top of it where the provider speaks it, as plain
  // functions. The providers are the files of subs/oauth/types/ (padKinds): a provider is
  // added by adding its file. What comes back is a user row for padLogin (lib/auth.php):
  //
  //   // apps/shop/oauth/[provider].php - the start and the answer of every provider
  //   if ( ! padRequestHas ( 'code' ) and ! padRequestHas ( 'error' ) )
  //     padOAuthStart ( $provider );                  // off to GitHub, state and PKCE kept
  //   if ( $user = padOAuthUser ( $provider ) ) {     // back: checked, exchanged, fetched
  //     padLogin ( $user );
  //     padRedirectIntended ();
  //   }
  //   $problem = padOAuthError ();
  //
  // padOAuthStart   sends the visitor to the provider's authorize page - a state, a PKCE
  //                 S256 challenge and (OpenID Connect) a nonce kept in the session first
  // padOAuthUrl     that address without going there - a test, a link of one's own
  // padOAuthUser    on the way back: the state checked, the code exchanged, the id_token
  //                 checked, the user fetched - the normalised user, or FALSE
  // padOAuthError   why the last padOAuthUser or padOAuthStart answered FALSE, '' when it
  //                 did not
  // padOAuthToken   the token endpoint's answer of the last login - access_token,
  //                 refresh_token, expires_in ... - for an application that calls the
  //                 provider's API afterwards; never in the user row, which goes into the
  //                 session
  // padOAuthKinds   the providers there are
  //
  // The user: provider (github), id (github:583231 - the provider's own id with the
  // provider in front, so two providers never give one id; for an instance other than the
  // provider's default its address too - gitlab@git.example.org:42, every OpenID issuer's
  // oidc@sso.example.com/realms/main:<sub> - as a sub is unique within its instance alone),
  // sub (583231), name, email, email_verified, avatar and username - '' where the provider has
  // none. The email is a contact: email_verified is TRUE only when the provider vouches that
  // the address is the account's - an application links a login to an existing account by
  // its address only then, else anyone who typed that address at the provider gets the
  // account. The code is in subs/oauth/_lib/oauth.php, read the first time one of these runs.
  //
  // A provider's client id and secret come from padEnv - GITHUB_CLIENT_ID and
  // GITHUB_CLIENT_SECRET in the application's .env - or from the options. A missing one is
  // an error under the strict check naming the variable to set; what goes wrong between the
  // visitor and the provider - a no at the provider, a stale or forged state, a refused code
  // - is no author's error: FALSE, and padOAuthError says what happened.

  function padOAuthStart ( $kind, $options = [] ) {

    include_once PAD . 'subs/oauth/_lib/oauth.php';

    $url = padOAuthAuthorize ( $kind, $options, 'padOAuthStart' );

    if ( $url === '' )
      return FALSE;

    padHeader ( "Location: $url" );

    padExit ( 302 );

  }

  function padOAuthUrl ( $kind, $options = [] ) {

    include_once PAD . 'subs/oauth/_lib/oauth.php';

    return padOAuthAuthorize ( $kind, $options, 'padOAuthUrl' );

  }

  function padOAuthUser ( $kind, $options = [] ) {

    include_once PAD . 'subs/oauth/_lib/oauth.php';

    return padOAuthCallback ( $kind, $options );

  }

  function padOAuthError () {

    return (string) padOAuthStore ( 'error' );

  }

  function padOAuthToken () {

    return padOAuthStore ( 'token' ) ?? [];

  }

  function padOAuthKinds () {

    return padKinds ( 'oauth' );

  }

  // This request's own memory - the last error and the last token answer - in a static, not
  // a global: an error report or a dump lists the globals, and a token is a credential.

  function padOAuthStore ( $key, $value = NULL ) {

    static $store = [];

    if ( func_num_args () > 1 )
      $store [$key] = $value;

    return $store [$key] ?? NULL;

  }

?>
