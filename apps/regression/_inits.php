<?php

  // Defaults for show/, which reads an application and an item out of the query string.

  if ( ! isset ( $app  ) ) $app  = 'manual';
  if ( ! isset ( $item ) ) $item = 'hello/hello';

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
  // regression2 for Pages, which runs with _common switched off, from regression3 for
  // Common, the pages that use it, and from regression4 for Framework, where every engine
  // case is a page of its own. Scan is the crawl of every application, comparing each page
  // against its stored copy - the three suite apps excepted, whose pages the suites assert.

  $skipTitle = TRUE;

  if     ( $padPage == 'index'                          ) $suiteTest = '?index&test';
  elseif ( $padPage == 'scan/index'                     ) $suiteTest = '?scan/index&go';
  elseif ( $padPage == 'pages/index'                    ) $suiteTest = '?pages/index&test';
  elseif ( $padPage == 'common/index'                   ) $suiteTest = '?common/index&test';
  elseif ( $padPage == 'framework/index'                ) $suiteTest = '?framework/index&test';
  else                                                    $suiteTest = '';

  $suites = [
    [ 'name' => 'Index',     'link' => '?index',           'now' => ( $padPage == 'index'           ) ? 1 : 0 ],
    [ 'name' => 'Pages',     'link' => '?pages/index',     'now' => ( $padPage == 'pages/index'     ) ? 1 : 0 ],
    [ 'name' => 'Common',    'link' => '?common/index',    'now' => ( $padPage == 'common/index'    ) ? 1 : 0 ],
    [ 'name' => 'Framework', 'link' => '?framework/index', 'now' => ( $padPage == 'framework/index' ) ? 1 : 0 ],
    [ 'name' => 'Scan',      'link' => '?scan/index',      'now' => ( $padPage == 'scan/index'      ) ? 1 : 0 ]
  ];

  if ( $suiteTest )
    $suites [] = [ 'name' => 'Test', 'link' => $suiteTest, 'now' => 0 ];

?>