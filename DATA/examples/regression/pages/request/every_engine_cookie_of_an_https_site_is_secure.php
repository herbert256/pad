<?php

  // A site that is https - the request came over TLS, the configuration says so with an
  // https $padHostBase (a proxy in front ends TLS and asks this server over plain http), or
  // php.ini asks it of the session cookie (session.cookie_secure) - sends every cookie of
  // the engine Secure: the session's, the flash sign's, padReqID. The request's HTTPS
  // alone decided, so behind such a proxy all of them went out without it, and the
  // operator's session.cookie_secure = 1 was turned off for the request by the engine.
  //
  // The page fetches itself three ways: plain http with no setting (no Secure), &behind
  // ($padHostBase https, in _config/config.php) and &ini (session.cookie_secure set here,
  // in the page's PHP - after padReqID went out, which stays plain in that one).

  if ( isset ( $_GET ['inner'] ) ) {

    if ( isset ( $_GET ['ini'] ) )
      ini_set ( 'session.cookie_secure', '1' );

    padSessionStart ();
    padFlash ( 'hi' );

    $secureResult = 'inner';

  } else {

    $secureResult = '';

    foreach ( [ 'plain' => '', 'behind' => '&behind', 'ini' => '&ini' ] as $secureName => $secureAsk ) {

      $secureGot = padCurl ( $padGoExt . "request/every_engine_cookie_of_an_https_site_is_secure&inner$secureAsk&padInclude" );

      $secureResult .= "$secureName:";

      foreach ( [ session_name (), 'padFlash', 'padReqID' ] as $secureCookie )
        $secureResult .= ' ' . $secureCookie . '='
                       . ( isset ( $secureGot ['cookieAttributes'] [$secureCookie] )
                           ? ( isset ( $secureGot ['cookieAttributes'] [$secureCookie] ['secure'] ) ? 'secure' : 'plain' )
                           : 'none' );

      $secureResult .= ' | ';

    }

  }

?>
