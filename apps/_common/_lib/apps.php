<?php

  // The application walkers, shared infrastructure: the suites in regression/main
  // enumerate their pages from here, develop's harvest and nuts pages walk the same list,
  // and record resolves a test name back to its application with the same boundary rule.

  // Every page of every application, walked once per request. Either half names a page,
  // so the .pad-only and .php-only forms both count and a pair counts once; a page with
  // no template that redirects, restarts or writes is an action, not a page, and a crawl
  // has to be able to run without changing anything.

  function padAppsList () {

    static $cache = NULL;

    if ( $cache !== NULL )
      return $cache;

    $directory = new RecursiveDirectoryIterator (APPS);
    $iterator  = new RecursiveIteratorIterator  ($directory);

    foreach ($iterator as $one ) {

      $path = padCorrectPath ( $one->getPathname() );

      if ( strpos ( $path, '/_') ) continue;

      $ext = substr($path, strrpos($path, '.')+1 );

      if ( $ext != 'pad' and $ext != 'php' and $ext != 'html' )
        continue;

      if ( $ext == 'php' and ! file_exists ( substr ( $path, 0, -4 ) . '.pad' )
                         and ! file_exists ( substr ( $path, 0, -4 ) . '.html' ) ) {

        $source = padFileGet ( $path );

        if ( str_contains ( $source, 'padRedirect'      )
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


  // An application is a name with an entry point: www/<app>/index.php is what makes a
  // name fetchable, so it is also what draws the app boundary inside a nested name.

  function padAppsListRoot ( $app ) {

    static $roots = [];

    return $roots [$app] ??= file_exists ( dirname ( APPS ) . "/www/$app/index.php" );

  }

?>