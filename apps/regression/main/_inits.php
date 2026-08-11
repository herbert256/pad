<?php

  // Every page of this application carries the same menu above its title. Its last entry is
  // Test, and what Test runs is about the page it is on: on the index everything, on a
  // suite page that suite, and on the scan page it is the scan. Pages that have no test of
  // their own, show/ among them, get the menu without the entry.
  //
  // $skipTitle turns off the <h1> the common wrapper prints - _exits.php of the _common app
  // reads it - so that _inits.pad can put those first and the title underneath. Without it
  // they could only ever appear below the title, because @page@ is what the wrapper replaces
  // and the title is already written by then.
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

  $skipTitle = TRUE;

  $suiteTest = '';

  if ( $padPage == 'index' )
    $suiteTest = '?index&test';

  elseif ( str_ends_with ( $padPage, '/index' )
           and isset ( getSuites () [ substr ( $padPage, 0, -6 ) ] ) )
    $suiteTest = "?$padPage&test";

  $suites = [ [ 'name' => 'Index', 'link' => '?index', 'now' => ( $padPage == 'index' ) ? 1 : 0 ] ];

  foreach ( array_keys ( getSuites () ) as $suiteEntry )
    $suites [] = [ 'name' => ucfirst ( $suiteEntry ),
                   'link' => "?$suiteEntry/index",
                   'now'  => ( $padPage == "$suiteEntry/index" ) ? 1 : 0 ];

  if ( $suiteTest )
    $suites [] = [ 'name' => 'Test', 'link' => $suiteTest, 'now' => 0 ];

  $suites [] = [ 'name' => 'Build', 'link' => '?build', 'now' => ( $padPage == 'build' ) ? 1 : 0 ];

?>