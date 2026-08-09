<?php

  $title = "Regression test";

  // The dashboard lists what the crawl stored, every page of every application the suites do
  // not already assert - regression2, regression3 and regression4 are left out. The suites
  // that assert an answer rather than only rendering one have a page of their own, reached
  // from the menu: the overview reports what each suite actually found, which is more use
  // than the status of its stored copy.

  // Test runs the suites and then crawls. The suites go first on purpose: a suite page reports
  // the last run rather than testing on sight, so crawling before them would store pages that
  // describe the previous run, and a case that started failing would not show until the crawl
  // after next.

  if ( isset ( $go ) ) {

    getRegression ();

    padRedirect ( $padPage );

  }

  // After a deliberate change, every page it touched is a warning; accepting them one link at
  // a time is what getRegressionWarning() replaces. It refetches each and stores what came
  // back, so read the list before asking for this - a page that keeps coming back has more to
  // say than a stale baseline.

  if ( isset ( $acceptWarnings ) ) {

    getRegressionWarning ( );

    padRedirect ( $padPage );

  }

  // The colour of every status a crawl can give a page, in the order the legend shows them.
  // They are declared here rather than in the template so that the legend and the list cannot
  // drift apart - both read this one list, and a status added to getRegressionGo() only has to
  // be given a colour here.

  $statusColors = [
    'ok'       => 'color:black;',
    'expected' => 'color:black;background-color: #d9d9d9;',
    'new'      => 'color:black;background-color: #248f24;',
    'warning' => 'color:black;background-color: #ffaa80;',
    'error'   => 'color:black;background-color: #f44336;',
    'random'  => 'color:black;background-color: #befffa;',
    'empty'   => 'color:black;background-color: #ffc0cb;'
  ];

  $list    = [];
  $counts  = [];
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

      $list [$app] ['app']                    = $app;
      $list [$app] ['items'] [$item] ['item'] = $item;
      $list [$app] ['items'] [$item] ['link'] = "?show&app=$app&item=" . urlencode ( $item );

      if ( $ext == 'txt') {

        $status = padFileGet ( $path );

        $list [$app] ['items'] [$item] ['status'] = $status;
        $list [$app] ['items'] [$item] ['style']  = $statusColors [$status] ?? '';

        $counts [$status] = ( $counts [$status] ?? 0 ) + 1;

      }

    }

    // Only what needs looking at is listed. A page that is ok has nothing to say and there are
    // hundreds of them - the list was seven hundred entries deep and the handful that mattered
    // were lost in it. They are still counted, so the legend above still reports every one.
    //
    // The status arrives on the .txt pass and an item is created on the .html one, so the drop
    // has to happen here rather than inside the loop: the item exists before its status is known.

    foreach ( $list as $app => $one ) {

      // An item with no status is not a result. The list is built from both halves - the .html
      // on one pass and the .txt on the other - so a stored page whose status file is missing
      // still made a row, with no status to colour it, and came out white and unexplained. Two
      // of those were left behind by a crawl called by hand with a bad item name.

      foreach ( $one ['items'] as $item => $what )
        if ( in_array ( $what ['status'] ?? '', [ 'ok', 'expected' ] ) or ! isset ( $what ['status'] ) )
          unset ( $list [$app] ['items'] [$item] );

      if ( ! count ( $list [$app] ['items'] ) )
        unset ( $list [$app] );

    }

    ksort ( $list );

    foreach ( $list as $key => $value )
      ksort ( $list [$key] ['items'] );

  }

  // Every colour is shown, including the ones no page has at the moment: a legend that leaves
  // out what is not there cannot be read as a key to what the colours mean.

  $statuses = [];
  $total    = 0;

  foreach ( $statusColors as $statusName => $statusStyle ) {

    $statuses [] = [
      'status' => $statusName,
      'count'  => $counts [$statusName] ?? 0,
      'style'  => $statusStyle
    ];

    $total += $counts [$statusName] ?? 0;

  }

  $warningCount = $counts ['warning'] ?? 0;

?>