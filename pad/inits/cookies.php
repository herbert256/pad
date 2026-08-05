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

      if ( ! isset($_COOKIE['padSesID']) or $_COOKIE['padSesID'] != $padSesID )
        setCookie ('padSesID', $padSesID, time() + (60 * 60 * 24 * 366 * 10) );

      setCookie ('padReqID', $padReqID, time() + (60 * 60 * 24 * 366 * 10) );

    }

?>