<?php

  if ( ! headers_sent () ) {

    if ( ! isset($_COOKIE['PADSESSID']) or $_COOKIE['PADSESSID'] <> $PADSESSID )
      setCookie ('PADSESSID', $PADSESSID, time() + (60 * 60 * 24 * 366 * 10) );

    setCookie ('PADREQID', $PADREQID, time() + (60 * 60 * 24 * 366 * 10) );

  }

?>