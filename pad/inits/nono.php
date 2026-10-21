<?php

  // The escape hatch for applications that want plain PHP with no templating at all, enabled
  // by setting $padNoNo in the application's _config/config.php (see the 'nono' app).
  //
  // Returns immediately for normal applications. Otherwise the page's .php file is run on its
  // own and the request ends there - no .pad template, no level loop, no exits. Every pad*
  // global is unset first so the page starts from a clean namespace and cannot accidentally
  // depend on engine state.
  //
  // Note this ends the request with exit, so nothing in exits/ runs: the page is responsible
  // for its own output and headers.

  // A page that is not there waits for inits/notFound.php (inits/page.php lets it, for an
  // application that is down for maintenance) - its name is no file to run. Down for
  // maintenance, the plain PHP is held back as every page is (lib/maintenance.php): a
  // plain 503, as there is no template to render one with.
  //
  // The stand-in page inits/page.php gives sitemap.xml, robots.txt and ?up, when the
  // application has no page of that name, waits as well: inits/sitemap.php and
  // inits/health.php answer it, or make it the 404 of a page that is not there. It was taken
  // for a page here, and the missing up.php ended every such request on a 500 boot error.
  // A bracketed route that took the name - [slug].php at the root - is the page only with
  // the sitemap or the health check off, as for any application: with it on the answer is
  // the engine's, where the route's plain PHP answered robots.txt, sitemap.xml and up.

  if ( ! $padNoNo or $padNotFound !== ''
       or ( $padSitemapAsk !== '' and ( $padSitemap or $padPage === $padSitemapAsk ) )
       or ( $padHealthAsk and ( $padHealth or $padPage === 'up' ) ) )
    return;

  if ( $padMaintenanceDown = padMaintenance () )
    padMaintenanceRefuse ( $padMaintenanceDown );

  $padNoNo = APP . "$padPage.php";

  if ( ! file_exists ( $padNoNo ) )
    padBootError ( "Page does not exists: $padNoNo" );

  foreach ( $GLOBALS as $key => $value )
    if ( substr ( $key, 0, 3 ) == 'pad' and $key != 'padNoNo' )
      unset ( $GLOBALS[$key] );

  unset ( $key );
  unset ( $value );

  include $padNoNo;

  exit;

?>
