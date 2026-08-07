<?php

  function getPage ( $page, $ignoreErrors=0, $include=1 ) {

    global $padGoExt;

    if ($include) $include = '&padInclude';
    else          $include = '';

    $url  = "$padGoExt$page$include";
    $curl = padCurl ($url);

    if ( ! $ignoreErrors and ! str_starts_with ( $curl ['result'], '2') )
      return padError ("Curl failed: $url");

    return $curl;

  }


  // Accepts every page the crawl has marked 'warning' - it walks the stored statuses and calls
  // ?ok on each, which is the one-page-at-a-time link the crawl list already offers.
  //
  // A warning means only that a page renders differently from the copy stored for it. After a
  // deliberate change - a case added, a menu entry, a wording fixed - that is every page the
  // change touched, and accepting them one link at a time is the tedium this replaces.
  //
  // It is not a way of making the list green. A page that renders differently every time it is
  // asked - a clock, a counter, an {ajax} id, anything drawing at random - comes straight back as
  // a warning on the next crawl, and should: the right answer for those is the 'random' marker in
  // the page, which stops the crawl comparing it at all. So read what this returns rather than
  // just running it; a name that keeps appearing is telling you something.
  //
  // Returns the items accepted, "app/item" each, in the order they were found.

  function regressionTestOK () {

    global $padHost;

    $done = [];
    $dir  = DATA . 'regression/';

    if ( ! is_dir ( $dir ) )
      return $done;

    $directory = new RecursiveDirectoryIterator ( $dir );
    $iterator  = new RecursiveIteratorIterator  ( $directory );

    foreach ( $iterator as $one ) {

      $path = padCorrectPath ( $one->getPathname () );

      if ( ! str_ends_with ( $path, '.txt' ) )
        continue;

      if ( trim ( padFileGet ( $path ) ) != 'warning' )
        continue;

      // The path under DATA/regression/ is the application, then the item - the same split the
      // crawl list makes, and the same two the ?ok page takes.

      $base = substr ( $path, strlen ( $dir ) );

      if ( ! str_contains ( $base, '/' ) )
        continue;

      list ( $app, $file ) = explode ( '/', $base, 2 );

      $item = substr ( $file, 0, -4 );

      set_time_limit ( 60 );

      padCurl ( $padHost . "regression/?ok&app=$app&item=" . urlencode ( $item ) );

      $done [] = "$app/$item";

    }

    sort ( $done );

    return $done;

  }

?>
