<?php

  // The page cache's policy, the part every backend shares: which requests may be answered
  // from it, and which finished pages may go into it. cache/inits.php and cache/exits.php
  // ask; the backends under cache/types/ only store and fetch.

  // A visitor with an identity: a cookie beyond PAD's own ids - the two ids and the locale - a PHP session, an
  // application's login - or an Authorization header. Their page may be theirs alone, and
  // the access checks a hit skips are what decides it; they are answered fresh and their
  // page is never stored. Served by URI alone, the first visitor's page went to everyone.

  function padCacheIdentity () {

    foreach ( array_keys ( $_COOKIE ) as $cookie )
      if ( ! in_array ( $cookie, [ 'padSesID', 'padReqID', 'padLang' ], TRUE ) )
        return TRUE;

    foreach ( [ 'HTTP_AUTHORIZATION', 'REDIRECT_HTTP_AUTHORIZATION', 'PHP_AUTH_USER' ] as $auth )
      if ( isset ( $_SERVER [$auth] ) )
        return TRUE;

    return FALSE;

  }

  // Whether a hit could send this page as this request sends it, to anyone: the configured
  // content type, status 200, no header the page's PHP added - a hit sends none of them -
  // and nothing of this visitor in it. A page that prints the visitor's own padSesID, as
  // {ajax} URLs do, handed that id to everyone who hit it - and a page holding the
  // session's CSRF token would hand that to everyone, whose own posts it then fails.

  function padCacheStorable () {

    global $padContentType, $padCacheContentType, $padSesID, $padReqID, $padOutput, $padCsrfIssued;

    if ( $padContentType !== ( $padCacheContentType ?? $padContentType ) )
      return FALSE;

    if ( http_response_code () and http_response_code () != 200 )
      return FALSE;

    foreach ( headers_list () as $header )
      if ( ! preg_match ( '/^(X-Powered-By:|Set-Cookie: pad(Ses|Req)ID=)/i', $header ) )
        return FALSE;

    foreach ( [ $padSesID ?? '', $padReqID ?? '', $padCsrfIssued ?? '' ] as $id )
      if ( $id !== '' and str_contains ( $padOutput, $id ) )
        return FALSE;

    return TRUE;

  }

  // Whether a directory guard (build/guards.php) stands over the request's page. A hit is
  // answered before any guard runs, and a guard may decide on more than the cookies the
  // cache already steps aside for - an address, a header, the time of day - so a guarded
  // page is neither served from the cache nor stored in it.

  function padCacheGuarded () {

    global $padDir;

    $dir = substr ( APP, 0, -1 );

    if ( file_exists ( "$dir/_guard.php" ) )
      return TRUE;

    foreach ( padExplode ( $padDir ?? '', '/' ) as $part ) {
      $dir .= "/$part";
      if ( file_exists ( "$dir/_guard.php" ) )
        return TRUE;
    }

    return FALSE;

  }

?>