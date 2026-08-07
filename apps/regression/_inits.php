<?php

  // Defaults for show/, which reads an application and an item out of the query string.

  if ( ! isset ( $app  ) ) $app  = 'manual';
  if ( ! isset ( $item ) ) $item = 'hello/hello';

  // Every page of this application carries the same menu above its title, and a Test link
  // where there is something to run: on a suite page that reruns that group, on the overview
  // every group, and on the crawl page it is the crawl. Pages that have no test of their own,
  // show/ among them, get the menu without the link.
  //
  // $skipTitle turns off the <h1> the common wrapper prints - _exits.php of the _common app
  // reads it - so that _inits.pad can put those first and the title underneath. Without it
  // they could only ever appear below the title, because @page@ is what the wrapper replaces
  // and the title is already written by then.
  //
  // The menu opens with the two suites and the crawl, then the sandbox groups getCases() knows
  // about, sorted, so a group added to _lib/cases.php turns up here without anything else being
  // touched. Each entry carries its own link, because neither ?all nor ?pages/index lives under
  // sandbox/.
  //
  // Sandbox and Pages are the two kinds of test: a sandbox case is a template rendered inside
  // this request, a pages test is a page of this application fetched over HTTP. The groups that
  // follow all belong to the first.

  $skipTitle = TRUE;

  if     ( $padPage == 'all'                            ) $suiteTest = '?all&go';
  elseif ( $padPage == 'pages/index'                    ) $suiteTest = '?pages/index&test';
  elseif ( str_starts_with ( $padPage, 'sandbox/' )     ) $suiteTest = "?$padPage&test";
  else                                                    $suiteTest = '';

  $suites = [
    [ 'name' => 'Sandbox', 'link' => '?sandbox/index', 'now' => ( $padPage == 'sandbox/index' ) ? 1 : 0 ],
    [ 'name' => 'Pages',   'link' => '?pages/index',   'now' => ( $padPage == 'pages/index'   ) ? 1 : 0 ],
    [ 'name' => 'All',     'link' => '?all',           'now' => ( $padPage == 'all'           ) ? 1 : 0 ]
  ];

  $suiteNames = array_keys ( getCasesGroups () );

  sort ( $suiteNames );

  foreach ( $suiteNames as $suiteName )
    $suites [] = [
      'name' => $suiteName,
      'link' => "?sandbox/$suiteName",
      'now'  => ( $padPage == "sandbox/$suiteName" ) ? 1 : 0
    ];

?>