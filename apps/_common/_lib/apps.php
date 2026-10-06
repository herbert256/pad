<?php

  // The application walkers, shared infrastructure: the suites in regression/main
  // enumerate their pages from here, develop's harvest and nuts pages walk the same list,
  // and record resolves a test name back to its application with the same boundary rule.

  // Every page of every application, walked once per request. Either half names a page,
  // so the .pad-only and .php-only forms both count and a pair counts once; a page with
  // no template that redirects (padRedirect, padBack), restarts or writes is an action,
  // not a page, and a crawl has to be able to run without changing anything.

  function padAppsList () {

    static $cache = NULL;

    if ( $cache !== NULL )
      return $cache;

    $directory = new RecursiveDirectoryIterator (APPS);
    $iterator  = new RecursiveIteratorIterator  ($directory);
    $files     = [];

    foreach ($iterator as $one ) {

      $path = padCorrectPath ( $one->getPathname() );

      // An _xxx directory of an application hides what is in it - asked of the part below
      // APPS, not of the whole path: a checkout that itself stands below an _xxx directory,
      // a CI runner's _work, had every path hidden, and the empty list ended on ksort(NULL).

      if ( str_contains ( '/' . substr ( $path, strlen ( APPS ) ), '/_' ) ) continue;

      $ext = substr($path, strrpos($path, '.')+1 );

      if ( $ext != 'pad' and $ext != 'php' and $ext != 'html' )
        continue;

      if ( $ext == 'php' and ! file_exists ( substr ( $path, 0, -4 ) . '.pad' )
                         and ! file_exists ( substr ( $path, 0, -4 ) . '.html' ) ) {

        $source = padFileGet ( $path );

        if ( str_contains ( $source, 'padRedirect'      )
          or str_contains ( $source, 'padBack'          )
          or str_contains ( $source, 'padRestart'       )
          or str_contains ( $source, 'padFilePut'       )
          or str_contains ( $source, 'padDeleteDataDir' ) )
          continue;

      }

      $file = str_replace ( APPS, '', $path );

      list ( $app, $item ) = padAppBoundary ( $file );

      $item = substr ( $item, 0, strrpos ( $item, '.' ) );

      // The runner's own tooling is not a page under test - develop is where builds and
      // cleanups are driven from, and fetching some of its pages does exactly that.

      if ( $app == 'develop' )
        continue;

      // A page no address reaches is no page of the walk: the router takes a literal segment
      // of letters, digits, _ and - only (lib/route.php), so v1.0/old and my.page answer 404
      // to every request, and the crawls fetched them all the same - develop/?nuts ended on
      // its {ajax} refusing them, a 500. A bracketed route stays: a path fills it in.

      if ( ! padAppsListRoutable ( $item ) )
        continue;

      $files ["$app/$item"] ['path'] = $path;
      $files ["$app/$item"] ['app']  = $app;
      $files ["$app/$item"] ['item'] = $item;

    }

    ksort ($files);

    return $cache = $files;

  }


  // The application inside a path: the shortest leading run of directories with an entry
  // point of its own, and what is left below it. A directory holding only applications,
  // like regression/, is a namespace, and an app below it keeps the namespace in its
  // name: 'regression/main'.

  function padAppBoundary ( $path ) {

    $parts = explode ( '/', dirname ( $path ) );
    $app   = array_shift ( $parts );

    while ( $parts and ! padAppsListRoot ( $app ) )
      $app .= '/' . array_shift ( $parts );

    return [ $app, substr ( $path, strlen ( $app ) + 1 ) ];

  }


  // Whether the router can reach a page by its name: each segment literal - letters,
  // digits, _ and -, as lib/route.php takes it - or bracketed, a route a path fills in.

  function padAppsListRoutable ( $item ) {

    foreach ( explode ( '/', $item ) as $segment )
      if ( ! preg_match ( '/^([a-zA-Z0-9][a-zA-Z0-9_-]*|\[[^\[\]\/]+\])$/D', $segment ) )
        return FALSE;

    return TRUE;

  }


  // An application is a name with an entry point: www/<app>/index.php is what makes a
  // name fetchable, so it is also what draws the app boundary inside a nested name.

  function padAppsListRoot ( $app ) {

    static $roots = [];

    return $roots [$app] ??= file_exists ( dirname ( APPS ) . "/www/$app/index.php" );

  }

?>
