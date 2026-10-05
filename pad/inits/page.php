<?php

  // Works out which page this request runs, and fails the request early if there is none.
  //
  // The name comes from whatever source applies: an already-set $padPage (a restart, or an
  // entry point that hard-codes it), otherwise the path of a clean URL (/shop/products/42,
  // inits/route.php) unless the query string starts with a page name of its own, otherwise
  // the first query string key - ?page/subpage - otherwise the first command line argument,
  // otherwise 'index'.
  //
  // The name is then verified against the application directory and resolved - a page of
  // its own, or a bracketed route like products/[id] whose segments become variables
  // (lib/route.php) - and $padDir (the page's subdirectory) and $padPath are derived from
  // it; build/ walks those to collect the _lib, _inits.pad and _exits.pad of every level.
  // $padStartPage remembers the page the request began with, so a restart can still tell
  // where it came from.
  //
  // A page under _tests/ is private like every _ directory, except to pad test on the
  // command line (padTestPageCheck in lib/page.php).

  if     ( isset($padPage) )                 $padPage = $padPage;
  elseif ( $padRoutePath !== ''
           and ! padRouteQuery () )          $padPage = $padRoutePath;
  elseif ( count($_GET) )                    $padPage = array_key_first ($_GET);
  elseif ( isset ( $_SERVER['argv'] [1] ) )  $padPage = $_SERVER['argv'] [1];
  else                                       $padPage = 'index';

  $padPage = padCorrectPath ( $padPage );

  // The name as it was asked for - products/42 - before it resolves to the file that
  // answers it, products/[id]: a redirect to this page, and the name of a download, are
  // the asked one.

  $padPageAsked = $padPage;

  // A page that is not there is a 404, the visitor's request rather than a server fault -
  // it was a 500 boot error. The name is shown to this machine's own requests only.

  $padRouteFound = padPageRoute ( $padPage );

  // An entry point may hand over the page's template as text, in $padPageSource - only
  // editors/render.php does, for the tooling that checks a template not saved yet or kept
  // in no file at all. Its page needs no file of its own then, only a well-formed name in
  // a directory of the application: the page's .php runs when there is one, and the
  // directory's _inits and _tags apply. The web entry never sets it, and no request value
  // fills a pad* name.

  $padPageVirtual = ( isset ( $GLOBALS ['padPageSource'] )
                      and ! $padRouteFound
                      and padPageVirtual ( $padPage ) );

  if ( $padPageVirtual )
    $padRouteFound = [ 'page' => $padPage, 'vars' => [] ];

  if ( ! $padRouteFound and padTestPageCheck ( $padPage ) )
    $padRouteFound = [ 'page' => $padPage, 'vars' => [] ];

  // sitemap.xml and robots.txt - ?sitemap.xml reaches PHP as sitemap_xml - are the engine's
  // to answer when the application has no page of that name (lib/sitemap.php). Whether it
  // answers them is a setting, and the configuration is read after this file, so the
  // question waits in $padSitemapAsk for inits/sitemap.php, on a stand-in page.

  $padSitemapAsk = '';

  if ( ! $padRouteFound and in_array ( $padPage, [ 'sitemap.xml', 'sitemap_xml', 'robots.txt', 'robots_txt' ], TRUE ) ) {
    $padSitemapAsk = str_replace ( '_', '.', $padPage );
    $padRouteFound = [ 'page' => $padSitemapAsk, 'vars' => [] ];
  }

  if ( ! $padRouteFound ) {

    while ( ob_get_level () )
      ob_end_clean ();

    if ( ! headers_sent () ) {
      http_response_code ( 404 );
      header ( 'Content-Type: text/plain; charset=UTF-8' );
    }

    echo padLocal () ? "Page '" . padMakeSafe ( $padPage, 100 ) . "' not found" : 'Page not found';

    $stop = 404;
    include PAD . 'exits/exit.php';

  }

  // A clean URL route binds its bracketed segments as variables of the request: they are
  // set before the request values are promoted (inits/parms.php), so ?id=7 cannot replace
  // the $id the path products/42 gave.

  $padPage      = $padRouteFound ['page'];
  $padRouteVars = $padRouteFound ['vars'];

  padRouteBind ( $padRouteVars );

  $padDir  = padDir  ();
  $padPath = padPath ();

  if ( ! isset ( $padStartPage) )
    $padStartPage = $padPage;

?>