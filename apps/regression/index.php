<?php

  $title = "Regression test";

  // Two views of the same crawl. 'tests' is the default and lists the suites under
  // regression/regression/, the pages that assert an answer rather than only rendering one;
  // 'all' is every page of every application, which is what this page used to open on.
  //
  // The crawl itself is not affected: both views read the same stored results, so switching
  // never means running anything again, and TEST still runs every page whichever view it was
  // started from - the view is carried through the redirect only so it is still there
  // afterwards.

  $only = ( $only ?? '' ) == 'all' ? 'all' : 'tests';

  if ( isset ( $go ) ) {
    getRegression ();
    padRedirect ( '', $only == 'all' ? [ 'only' => 'all' ] : [] );
  }

  $list = [];
  $regPath = DATA . 'regression/';

  if ( is_dir ( $regPath ) ) {

    $directory = new RecursiveDirectoryIterator ( $regPath );
    $iterator  = new RecursiveIteratorIterator  ( $directory  );

    foreach ($iterator as $one ) {

      $path = $one->getPathname() ;
      $ext  = $one->getExtension() ;

      if ( $ext != 'html' and $ext != 'txt' ) continue;

      $base = str_replace ( $regPath, '', $path );
      list ( $app, $file ) = explode ( '/', $base, 2 );
      $item = substr ( $file, 0, strrpos ( $file, '.') );

      // The suites are the pages of the regression application that sit under regression/,
      // which is the whole of what regression/regression/ holds and nothing else.

      if ( $only == 'tests' )
        if ( $app != 'regression' or ! str_starts_with ( $item, 'regression/' ) )
          continue;

      // A suite is named for what it covers, so it is listed under that name alone, and the
      // link opens the page itself - a suite reports its own cases, which is more use than the
      // stored copy that show/ displays. Everything else keeps the name it is stored under and
      // goes to show/, the only way to see a page that belongs to another application.

      if ( $only == 'tests' ) {
        $label = substr ( $item, strlen ( 'regression/' ) );
        $link  = "?$item";
      }
      else {
        $label = $item;
        $link  = "?show&app=$app&item=$item";
      }

      $list [$app] ['app']                     = $app;
      $list [$app] ['items'] [$item] ['item']  = $item;
      $list [$app] ['items'] [$item] ['label'] = $label;
      $list [$app] ['items'] [$item] ['link']  = $link;

      if ( $ext == 'txt')
        $list [$app] ['items'] [$item] ['status'] = padFileGet ( $path );

    }

    ksort ( $list );

    foreach ( $list as $key => $value )
      ksort ( $list [$key] ['items'] );

  }

?>