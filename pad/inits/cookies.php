<?php

  if ( $padCookies )
    
    if ( ! headers_sent () ) {

      if ( ! isset($_COOKIE['padSesID']) or $_COOKIE['padSesID'] <> $padSesID )
        setCookie ('padSesID', $padSesID, time() + (60 * 60 * 24 * 366 * 10) );

      setCookie ('padReqID', $padReqID, time() + (60 * 60 * 24 * 366 * 10) );

    }

?>