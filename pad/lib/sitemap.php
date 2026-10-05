<?php

  // The sitemap, generated from the file tree: in PAD every page is a file, so the list of
  // pages needs no route table - the application directory is the list.
  //
  //   {sitemap} ... {/sitemap}          every page as a row: page, url, lastmod
  //   {sitemap 'docs'} ... {/sitemap}   the pages below one directory
  //   ?sitemap.xml   /shop/sitemap.xml  the XML sitemap, with $padSitemap on
  //   ?robots.txt    /shop/robots.txt   a robots.txt that points to it
  //
  // The walk leaves out what is no page a visitor should be sent to: _ and dot entries (the
  // _lib, _include, _inits files and their kind), a directory with a _guard.php in it (its
  // pages are guarded), a bracketed route like products/[id] (it has no one address), a page
  // with no template whose PHP only redirects - padBack included - restarts or writes (an
  // action, like todoPost), a page whose template says {meta sitemap=false}, and every name
  // $padSitemapSkip lists - a page, or a directory and all below it. lastmod is the newest
  // time among the page's files.
  //
  // padSitemapPages  the rows, the index of a directory before its other pages, then its
  //                  subdirectories, each sorted
  // padSitemapWalk   one directory of that walk
  // padSitemapKeep   whether one page belongs in it
  // padSitemapUrl    the address of a page: the application itself for index, a directory
  //                  for its index, $padGoExt for the rest - so the clean form follows
  //                  $padCleanUrls
  // padSitemapTime   the newest modification time among a page's .pad, .php and .html
  // padSitemapXml    the sitemap protocol's document over those rows
  // padSitemapRobots the robots.txt that names it
  // padSitemapAnswer the request for one of the two answered, when the application has no
  //                  page of that name and $padSitemap is on

  // A directory that holds a _guard.php is left out with everything below it - the walk
  // passes over such a subdirectory, and the same holds for the application root and for
  // every directory down to the one asked for, which the walk starts below: an application
  // guarded at its root listed every page its guard stands over, in a sitemap.xml that is
  // answered before any guard runs, and {sitemap 'admin'} listed admin/'s guarded pages.

  function padSitemapPages ( $dir = '' ) {

    $dir   = trim ( (string) $dir, '/' );
    $check = APP;

    foreach ( array_merge ( [ '' ], $dir === '' ? [] : explode ( '/', $dir ) ) as $part ) {
      $check .= ( $part === '' ) ? '' : "$part/";
      if ( file_exists ( $check . '_guard.php' ) )
        return [];
    }

    return padSitemapWalk ( $dir === '' ? '' : "$dir/" );

  }

  function padSitemapWalk ( $prefix ) {

    $path = APP . $prefix;

    if ( ! is_dir ( $path ) )
      return [];

    $entries = scandir ( $path );

    sort ( $entries );

    $pages = $dirs = [];

    foreach ( $entries as $entry ) {

      if ( str_starts_with ( $entry, '_' ) or str_starts_with ( $entry, '.' ) or str_contains ( $entry, '[' ) )
        continue;

      if ( is_dir ( "$path$entry" ) ) {
        if ( ! file_exists ( "$path$entry/_guard.php" ) )
          $dirs [] = $entry;
        continue;
      }

      if ( preg_match ( '/^([a-zA-Z0-9][a-zA-Z0-9_-]*)\.(pad|php|html)$/D', $entry, $match ) )
        $pages [ $match [1] ] = TRUE;

    }

    $pages = array_keys ( $pages );

    usort ( $pages, fn ( $a, $b ) => ( $a == 'index' ) ? -1 : ( ( $b == 'index' ) ? 1 : strcmp ( $a, $b ) ) );

    $rows = [];

    foreach ( $pages as $page )
      if ( padSitemapKeep ( $prefix, $page ) )
        $rows [] = [ 'page'    => "$prefix$page",
                     'url'     => padSitemapUrl ( "$prefix$page" ),
                     'lastmod' => padSitemapTime ( APP . "$prefix$page" ) ];

    foreach ( $dirs as $one )
      $rows = array_merge ( $rows, padSitemapWalk ( "$prefix$one/" ) );

    return $rows;

  }

  function padSitemapKeep ( $prefix, $page ) {

    global $padSitemapSkip;

    $name = "$prefix$page";
    $base = APP . $name;

    foreach ( (array) ( $padSitemapSkip ?? [] ) as $skip ) {
      $skip = trim ( (string) $skip, '/' );
      if ( $skip !== '' and ( $name === $skip or str_starts_with ( $name, "$skip/" ) ) )
        return FALSE;
    }

    $template = file_exists ( "$base.pad" ) ? "$base.pad" : ( file_exists ( "$base.html" ) ? "$base.html" : '' );

    if ( $template === '' ) {

      $source = padFileGet ( "$base.php" );

      foreach ( [ 'padRedirect', 'padBack', 'padRestart', 'padFilePut', 'padExit' ] as $action )
        if ( str_contains ( $source, $action ) )
          return FALSE;

      return TRUE;

    }

    return ! preg_match ( '/\{meta\b[^}]*\bsitemap\s*=\s*([\'"]?)(false|no|0)\1/i', padFileGet ( $template ) );

  }

  function padSitemapUrl ( $page ) {

    global $padApp, $padGoExt, $padHost;

    if ( $page == 'index' )
      return $padHost . "$padApp/";

    if ( str_ends_with ( $page, '/index' ) )
      $page = substr ( $page, 0, -6 );

    return $padGoExt . $page;

  }

  function padSitemapTime ( $base ) {

    $time = 0;

    foreach ( [ 'pad', 'php', 'html' ] as $ext )
      if ( file_exists ( "$base.$ext" ) )
        $time = max ( $time, filemtime ( "$base.$ext" ) );

    return gmdate ( 'Y-m-d', $time );

  }

  function padSitemapXml ( $rows ) {

    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
         . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    foreach ( $rows as $row )
      $xml .= '  <url><loc>' . htmlspecialchars ( $row ['url'], ENT_XML1 | ENT_QUOTES, 'UTF-8' ) . '</loc>'
            . '<lastmod>' . $row ['lastmod'] . '</lastmod></url>' . "\n";

    return $xml . '</urlset>' . "\n";

  }

  function padSitemapRobots () {

    global $padGoExt;

    return "User-agent: *\nAllow: /\nSitemap: {$padGoExt}sitemap.xml\n";

  }

  // inits/page.php found no page of the name and left the question here, with the
  // configuration now read: answered when $padSitemap is on, the same 404 as any missing
  // page when it is off.

  function padSitemapAnswer ( $what ) {

    global $padSitemap;

    while ( ob_get_level () )
      ob_end_clean ();

    if ( ! $padSitemap ) {

      if ( ! headers_sent () ) {
        http_response_code ( 404 );
        header ( 'Content-Type: text/plain; charset=UTF-8' );
      }

      echo padLocal () ? "Page '$what' not found" : 'Page not found';

      $stop = 404;
      include PAD . 'exits/exit.php';

    }

    if ( $what == 'robots.txt' ) {
      $type = 'text/plain; charset=UTF-8';
      $body = padSitemapRobots ();
    } else {
      $type = 'application/xml; charset=UTF-8';
      $body = padSitemapXml ( padSitemapPages () );
    }

    if ( ! headers_sent () ) {
      http_response_code ( 200 );
      header ( "Content-Type: $type" );
      header ( 'Content-Length: ' . strlen ( $body ) );
    }

    echo $body;

    $stop = 200;
    include PAD . 'exits/exit.php';

  }

?>