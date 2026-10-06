<?php

  function examplesBuild () {

    foreach ( padAppsList () as $one ) {

      set_time_limit ( 60 );

      examplesBuildPage ( $one ['app'], $one ['item'] );

    }

  }

  function examplesBuildPage ( $app, $item ) {

    global $padHost;

    if ( examplesGuarded ( $app, $item ) )
      return;

    $curl = padCurl ( "$padHost$app/?$item&padInclude" );

    if ( ! str_starts_with ( $curl ['result'], '2' ) )
      return;

    $source = padFileGet ( APPS . "$app/$item.pad"  )
            . padFileGet ( APPS . "$app/$item.html" )
            . padFileGet ( APPS . "$app/$item.php"  );

    if ( str_contains ( $source, '{page'    ) ) return;
    if ( str_contains ( $source, '{example' ) ) return;
    if ( str_contains ( $source, '{ajax'    ) ) return;
    if ( str_contains ( $source, '{table'   ) ) return;
    if ( str_contains ( $source, '{demo'    ) ) return;

    if ( file_exists ( APPS . "$app/$item.php" ) )
      padFilePut ( "examples/$app/$item.php",  padFileGet ( APPS . "$app/$item.php" ) );

    if ( file_exists ( APPS . "$app/$item.pad" ) )
      padFilePut ( "examples/$app/$item.pad",  padFileGet ( APPS . "$app/$item.pad" ) );
    elseif ( file_exists ( APPS . "$app/$item.html" ) )
      padFilePut ( "examples/$app/$item.pad",  padFileGet ( APPS . "$app/$item.html" ) );

    padFilePut ( "examples/$app/$item.html", padTidySmall ( $curl ['data'], TRUE ) );

  }

  // A page behind a _guard.php - in its own directory or any above it, up to the application
  // root - is no example: what a crawl gets there is the guard's answer, a refusal or a
  // login page, and a login page carries its session's own CSRF token, so every harvest
  // stored a different file. The sitemap leaves the same pages out (lib/sitemap.php).

  function examplesGuarded ( $app, $item ) {

    $dir   = APPS . "$app/";
    $parts = explode ( '/', $item );

    array_pop ( $parts );

    if ( file_exists ( $dir . '_guard.php' ) )
      return TRUE;

    foreach ( $parts as $part ) {
      $dir .= "$part/";
      if ( file_exists ( $dir . '_guard.php' ) )
        return TRUE;
    }

    return FALSE;

  }

  // This machine, asking from one of its own pages or an address typed in - as develop's
  // tools ask (developLocal, apps/develop/_lib/develop.php): the command line, or loopback
  // with nothing forwarded (padLocal); this machine's name in the Host header, which a page
  // on another site that has its own name resolve to 127.0.0.1 does not send, and which
  // $padHost - the host the crawl fetches from - is made of; and not sent by a page of
  // another site, which a browser says in Sec-Fetch-Site.

  function examplesLocal () {

    if ( PHP_SAPI === 'cli' )
      return TRUE;

    $host = strtolower ( preg_replace ( '/:\d+$/', '', trim ( (string) ( $_SERVER ['HTTP_HOST'] ?? '' ) ) ) );
    $site = strtolower ( trim ( (string) ( $_SERVER ['HTTP_SEC_FETCH_SITE'] ?? '' ) ) );

    return padLocal ()
       and in_array ( $host, [ 'localhost', '127.0.0.1', '[::1]' ], TRUE )
       and $site !== 'cross-site' and $site !== 'same-site';

  }

?>
