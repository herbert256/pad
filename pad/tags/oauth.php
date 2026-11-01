<?php

  // {oauth 'github'/} - a "Log in with GitHub" link styled as a button, for the OAuth
  // subsystem of subs/oauth/: the first parameter is the provider, a file of
  // subs/oauth/types/ (padOAuthKinds) - apple, bitbucket, discord, github, google, oidc ... It
  // links to the page oauth/<provider> of the application - a page oauth/[provider].php that
  // calls padOAuthStart and, on the way back, padOAuthUser (lib/oauth.php) - unless href=
  // names another; label= is its text instead of Log in with <name>. The provider's icon
  // shows when subs/icons/ has one of its name, else the log-in icon.
  //
  //   {oauth 'github'/}
  //   {oauth 'oidc', href='?login/keycloak', label='Log in with your company account'/}
  //
  // An unknown provider is an error under the strict check, the providers named. The button
  // needs no credentials - the page it links to does.

  include_once PAD . 'subs/oauth/_lib/oauth.php';
  include_once PAD . 'subs/oauth/_lib/button.php';

  $padOAuthKind = strtolower ( trim ( padParmText ( (string) $padParm ) ) );

  if ( ! padKind ( 'oauth', $padOAuthKind ) ) {
    if ( $padCheckSyntax )
      padError ( "the {oauth} has no provider named '" . padMakeSafe ( $padOAuthKind, 30 ) . "' - " . implode ( ', ', padKinds ( 'oauth' ) ) );
    return '';
  }

  if ( $padInfoXref ?? FALSE )
    padInfoXref ( 'oauth', $padOAuthKind );

  return padOAuthButton ( $padOAuthKind,
                          padParmText ( (string) padTagParm ( 'href', $padGo . "oauth/$padOAuthKind" ) ),
                          trim ( padParmText ( (string) padTagParm ( 'label', '' ) ) ) );

?>
