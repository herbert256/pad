<?php

  $title = "Regression test";

  // The dashboard lists what the crawl stored, every page of every application. The suites
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

  // The colour of every status a crawl can give a page, in the order the legend shows them.
  // They are declared here rather than in the template so that the legend and the list cannot
  // drift apart - both read this one list, and a status added to getRegressionGo() only has to
  // be given a colour here.

  $statusColors = [
    'ok'      => 'color:black;',
    'new'     => 'color:black;background-color: #248f24;',
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
      $list [$app] ['items'] [$item] ['link'] = "?show&app=$app&item=$item";

      if ( $ext == 'txt') {

        $status = padFileGet ( $path );

        $list [$app] ['items'] [$item] ['status'] = $status;
        $list [$app] ['items'] [$item] ['style']  = $statusColors [$status] ?? '';

        $counts [$status] = ( $counts [$status] ?? 0 ) + 1;

      }

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

?>