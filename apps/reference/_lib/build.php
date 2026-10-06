<?php

  function referenceBuild () {

    foreach ( padAppsList () as $one ) {

      set_time_limit ( 60 );

      referenceBuildPage ( $one ['app'], $one ['item'] );

    }

  }

  function referenceBuildPage ( $app, $item ) {

    global $padHost;

    $include = ( $item != 'index' ) ? '&padInclude' : '';

    $curl = padCurl ( "$padHost$app/?$item$include&padReference" );

  }

  // This machine, asking from one of its own pages or an address typed in - as develop's
  // tools ask (developLocal, apps/develop/_lib/develop.php): the command line, or loopback
  // with nothing forwarded (padLocal); this machine's name in the Host header, which a page
  // on another site that has its own name resolve to 127.0.0.1 does not send, and which
  // $padHost - the host the crawl fetches from - is made of; and not sent by a page of
  // another site, which a browser says in Sec-Fetch-Site.

  function referenceLocal () {

    if ( PHP_SAPI === 'cli' )
      return TRUE;

    $host = strtolower ( preg_replace ( '/:\d+$/', '', trim ( (string) ( $_SERVER ['HTTP_HOST'] ?? '' ) ) ) );
    $site = strtolower ( trim ( (string) ( $_SERVER ['HTTP_SEC_FETCH_SITE'] ?? '' ) ) );

    return padLocal ()
       and in_array ( $host, [ 'localhost', '127.0.0.1', '[::1]' ], TRUE )
       and $site !== 'cross-site' and $site !== 'same-site';

  }

?>
