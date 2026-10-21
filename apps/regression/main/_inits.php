<?php

  // Every page of this application has the frame every PAD application shares
  // (_common/_tags/padFrame.pad), and the same menu in it: Index, the suites, then Test and
  // Build. What Test runs is about the page it is on: on the index everything, on a suite
  // page that suite. Pages that have no test of their own, show/ among them, get the menu
  // without the entry.
  //
  // Index is the overview the application opens on, one line of totals per kind of test.
  // Pages, Common and Framework are the suites, every test a page fetched over HTTP - from
  // regression/pages for Pages, which runs with _common switched off, from regression/common for
  // Common, the pages that use it, and from regression/framework for Framework, where every engine
  // case is a page of its own. Regression is the suite over the self-testing applications,
  // their predictions held in regression/regression while the pages stay in their own
  // apps. Sequence and Manual are the application suites - apps/sequence/ and apps/manual/,
  // their predictions in regression/sequence and regression/manual the same way. Other is
  // the suite over every application without one of its own. Build is the fresh build:
  // clear the suite results and the dumps, and run the eight suites - the reference and
  // the examples stores are develop's, harvested there and left standing.

  $suiteTest = '';

  if ( $padPage == 'index' )
    $suiteTest = '?index&test';

  elseif ( str_ends_with ( $padPage, '/index' )
           and isset ( getSuites () [ substr ( $padPage, 0, -6 ) ] ) )
    $suiteTest = "?$padPage&test";

  $frameMenu = [ [ 'link' => '?index', 'label' => 'Index', 'icon' => 'home', 'current' => $padPage == 'index' ],
                 [ 'head' => 'Suites' ] ];

  foreach ( array_keys ( getSuites () ) as $suiteEntry )
    $frameMenu [] = [ 'link'    => "?$suiteEntry/index",
                      'label'   => ucfirst ( $suiteEntry ),
                      'icon'    => 'check-circle',
                      'current' => $padPage == "$suiteEntry/index" ];

  $frameMenu [] = [ 'head' => 'Run' ];

  if ( $suiteTest )
    $frameMenu [] = [ 'link' => $suiteTest, 'label' => 'Test', 'icon' => 'play' ];

  $frameMenu [] = [ 'link' => '?build', 'label' => 'Build', 'icon' => 'refresh', 'current' => $padPage == 'build' ];

?>
