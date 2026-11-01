<?php

  // The {oauth 'github'/} button: a link styled as a button, Log in with <the provider's name>,
  // with the provider's icon when subs/icons/ has one of its name - else the log-in icon. Its
  // style is www/assets/oauth/oauth.css, written once per page (padWidgetStyle), its colours
  // --pad-oauth-* custom properties with light-dark() defaults. Only the provider's name and
  // icon are read here: no setting, no request - the button renders without credentials, and
  // padOAuthStart on the page it links to names what is missing.

  function padOAuthButton ( $kind, $href, $label ) {

    include_once PAD . 'subs/icons/_lib/icon.php';

    $provider = padOAuthInclude ( PAD . "subs/oauth/types/$kind.php", [] );
    $name     = (string) ( $provider ['name'] ?? $kind );
    $icon     = (string) ( $provider ['icon'] ?? '' );
    $label    = ( $label !== '' ) ? $label : "Log in with $name";
    $svg      = ( $icon !== '' and padKind ( 'icons', $icon ) ) ? padIcon ( $icon, 18 ) : padIcon ( 'log-in', 18 );

    return padOAuthStyle ()
         . '<a class="pad-oauth pad-oauth-' . padWidgetAttr ( $kind ) . '" href="' . padWidgetAttr ( $href ) . '">'
         . $svg . '<span>' . padWidgetAttr ( $label ) . '</span></a>';

  }

  function padOAuthStyle () {

    $rules = padWidgetFile ( 'oauth/oauth.css' );

    return padWidgetStyle ( 'oauth', [ 'back'  => [ '#ffffff', '#1f2328' ],
                                       'text'  => [ '#1f2328', '#f0f3f6' ],
                                       'line'  => [ '#c8ccd1', '#4a5058' ],
                                       'hover' => [ '#f3f4f6', '#2d333b' ],
                                       'focus' => [ '#2563eb', '#7aa2f7' ] ], $rules );

  }

?>
