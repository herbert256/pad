<?php

  // Sends the two identity cookies established by inits/ids.php back to the browser, both
  // with a ten year life: padSesID only when it differs from what the browser already sent,
  // padReqID always, since it changes every request and is what the next request reports as
  // its parent.
  //
  // Skipped entirely when the application turns $padCookies off, and silently when headers
  // have already gone out - which is the normal case on a restart.

  if ( $padCookies )

    if ( ! headers_sent () ) {

      // Kept from scripts and from cross-site requests, and over https only when the site
      // is https (padCookieSecure, lib/session.php): they were plain cookies any script on
      // the page could read.

      $padCookieFlags = [ 'expires'  => time() + (60 * 60 * 24 * 366 * 10),
                          'httponly' => TRUE,
                          'samesite' => 'Lax',
                          'secure'   => padCookieSecure () ];

      // On the application's directory - /pad/shop/ - as the address of the entry point
      // shows it: with no path the browser kept each pair on the directory of the address,
      // so under clean URLs /shop/products/42 and /shop/docs/a/b each got a padSesID and a
      // padReqID of their own, one visitor had a session id per directory, and the parent
      // a request reported was the last one of its directory. Where the address does not
      // begin with the entry point's directory - an Alias, a proxy's prefix - the browser's
      // own choice stands: that directory is no path the browser would send them back to,
      // as it does for a directory setcookie cannot write as a path (a ',' ';' or space).

      $padCookieDir = rtrim ( str_replace ( '\\', '/', dirname ( (string) ( $_SERVER ['SCRIPT_NAME'] ?? '' ) ) ), '/' ) . '/';

      if (     str_starts_with ( (string) ( $_SERVER ['REQUEST_URI'] ?? '' ), $padCookieDir )
           and strpbrk ( $padCookieDir, ",; \t\r\n\013\014" ) === FALSE ) {

        $padCookieFlags ['path'] = $padCookieDir;

        // A pair the browser kept on a directory below it before - the address's own
        // directory, and each one between - is sent first, the longer path ahead, and PHP
        // keeps the first of two cookies of one name: that old padReqID was every later
        // request's parent, for the ten years it lives. It is expired where it may stand,
        // and the session id it carried goes on on the application's directory.
        //
        // Only when the Cookie header names one of them twice - an old copy and the one on
        // the application's directory, both matching this address: a name sent once is the
        // only copy, there is nothing behind it to clean up, and a returning visitor's every
        // clean-URL request got two expiring Set-Cookie lines per directory for nothing. A
        // directory setcookie cannot carry as a path - a ',' ';' or white space in a segment
        // of the address - is passed over: setcookie refuses it with an Error, which ended
        // the request with a 500, and a browser never matches a path written another way.

        $padCookieOld = explode ( '?', (string) $_SERVER ['REQUEST_URI'], 2 ) [0];
        $padCookieOld = substr ( $padCookieOld, 0, (int) strrpos ( $padCookieOld, '/' ) );

        if (    preg_match_all ( '/(?:^|;)\s*padReqID=/', (string) ( $_SERVER ['HTTP_COOKIE'] ?? '' ) ) > 1
             or preg_match_all ( '/(?:^|;)\s*padSesID=/', (string) ( $_SERVER ['HTTP_COOKIE'] ?? '' ) ) > 1 )
          while ( strlen ( $padCookieOld ) >= strlen ( $padCookieDir ) and str_starts_with ( $padCookieOld, $padCookieDir ) ) {
            if ( strpbrk ( $padCookieOld, ",; \t\r\n\013\014" ) === FALSE )
              foreach ( [ 'padSesID', 'padReqID' ] as $padCookieName )
                setCookie ( $padCookieName, '', [ 'expires' => 1, 'path' => $padCookieOld ] + $padCookieFlags );
            $padCookieMoved = TRUE;
            $padCookieOld = substr ( $padCookieOld, 0, (int) strrpos ( $padCookieOld, '/' ) );
          }

      }

      if ( ! isset($_COOKIE['padSesID']) or $_COOKIE['padSesID'] != $padSesID or isset ( $padCookieMoved ) )
        setCookie ('padSesID', $padSesID, $padCookieFlags );

      setCookie ('padReqID', $padReqID, $padCookieFlags );

    }

?>
