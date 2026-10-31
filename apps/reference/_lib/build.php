<?php

  // Every page of every application - and the pages of a bracketed route, [toy].pad, by the
  // names the application's own pages link to: a route has no name of its own to ask by, so
  // ?[toy] answered 404 and what the toys and the games application show on a page each was
  // on no page of the reference.

  function referenceBuild () {

    $routes = [];

    foreach ( padAppsList () as $one ) {

      set_time_limit ( 60 );

      if ( str_contains ( $one ['item'], '[' ) )
        continue;

      $answer = referenceBuildPage ( $one ['app'], $one ['item'] );

      foreach ( referenceBuildRoutes ( $one ['app'], $answer ) as $page )
        $routes [ $one ['app'] ] [$page] = TRUE;

    }

    foreach ( $routes as $app => $pages )
      foreach ( array_keys ( $pages ) as $page ) {
        set_time_limit ( 60 );
        referenceBuildPage ( $app, $page );
      }

    referenceBuildWrappers ();

  }

  // The tags of the _common application an application's wrappers write - {common:padFrame}
  // and {common:padFrameEnd/} in its _inits.pad - filed under its index, as the record of a
  // page would have them: a crawl renders each page without its wrappers (padReference, as
  // an include), so the frame every PAD application opens was on no page of the reference.

  function referenceBuildWrappers () {

    $apps = array_unique ( array_column ( padAppsList (), 'app' ) );

    foreach ( $apps as $app )
      foreach ( [ '_inits.pad', '_exits.pad' ] as $wrapper ) {

        $file = APPS . "$app/$wrapper";

        if ( ! file_exists ( $file ) or ! preg_match_all ( '/\{common:([A-Za-z][A-Za-z0-9_]*)/', padFileGet ( $file ), $names ) )
          continue;

        foreach ( array_unique ( $names [1] ) as $name ) {
          $target = "reference/tag/common/$name.txt";
          if ( ! file_exists ( DATA . $target ) or ! in_array ( "$app;index", file ( DATA . $target, FILE_IGNORE_NEW_LINES ) ) )
            padFilePut ( $target, "$app;index", 1 );
        }

      }

  }

  function referenceBuildPage ( $app, $item ) {

    global $padHost;

    $include = ( $item != 'index' ) ? '&padInclude' : '';

    return (string) ( padCurl ( "$padHost$app/?" . str_replace ( '%2F', '/', rawurlencode ( $item ) ) . "$include&padReference" ) ['data'] ?? '' );

  }

  // The pages of a bracketed route an answer links to - ?life, /pad/toys/?life - each once: a
  // link of the application itself whose page name no file has and a route takes.

  function referenceBuildRoutes ( $app, $answer ) {

    $found = [];

    preg_match_all ( '/href="([^"]*)"/', $answer, $links );

    foreach ( $links [1] as $link ) {

      $link = html_entity_decode ( $link, ENT_QUOTES | ENT_HTML5 );

      if ( ! preg_match ( '#^(?:[^?\#]*/' . preg_quote ( $app, '#' ) . '/)?\?([^&=\#]+)(?=$|[&\#])#', $link, $match ) )
        continue;

      if ( str_contains ( $link, '://' ) or ( $link [0] != '?' and ! str_contains ( $link, "/$app/" ) ) )
        continue;

      $page  = rawurldecode ( $match [1] );
      $route = padPageRoute ( $page, APPS . "$app/" );

      if ( $route and $route ['vars'] )
        $found [$page] = TRUE;

    }

    return array_keys ( $found );

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
