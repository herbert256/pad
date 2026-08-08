<?php

  // Defaults for show/, which reads an application and an item out of the query string.

  if ( ! isset ( $app  ) ) $app  = 'manual';
  if ( ! isset ( $item ) ) $item = 'hello/hello';

  // Every page of this application carries the same menu above its title. Its last entry is
  // Test, and what Test runs is about the page it is on: on the index all four kinds, on a
  // suite page that suite, on a sandbox group page that group, on the sandbox overview every
  // group, and on the scan page it is the scan. Pages that have no test of their own, show/
  // among them, get the menu without the entry.
  //
  // $skipTitle turns off the <h1> the common wrapper prints - _exits.php of the _common app
  // reads it - so that _inits.pad can put those first and the title underneath. Without it
  // they could only ever appear below the title, because @page@ is what the wrapper replaces
  // and the title is already written by then.
  //
  // Index is the overview the application opens on, one line of totals per kind of test.
  // Sandbox, Pages and Common are the kinds: a sandbox case is a template rendered inside
  // this request, a pages test is a page fetched over HTTP - from regression2 for Pages,
  // which runs with _common switched off, and from regression3 for Common, the pages that use
  // it. Scan is the crawl of every page of every application. The sandbox groups are not menu
  // entries; the Sandbox overview lists them.

  $skipTitle = TRUE;

  if     ( $padPage == 'index'                          ) $suiteTest = '?index&test';
  elseif ( $padPage == 'scan/index'                     ) $suiteTest = '?scan/index&go';
  elseif ( $padPage == 'pages/index'                    ) $suiteTest = '?pages/index&test';
  elseif ( $padPage == 'common/index'                   ) $suiteTest = '?common/index&test';
  elseif ( str_starts_with ( $padPage, 'sandbox/' )     ) $suiteTest = "?$padPage&test";
  else                                                    $suiteTest = '';

  $suites = [
    [ 'name' => 'Index',   'link' => '?index',         'now' => ( $padPage == 'index'         ) ? 1 : 0 ],
    [ 'name' => 'Sandbox', 'link' => '?sandbox/index', 'now' => ( $padPage == 'sandbox/index' ) ? 1 : 0 ],
    [ 'name' => 'Pages',   'link' => '?pages/index',   'now' => ( $padPage == 'pages/index'   ) ? 1 : 0 ],
    [ 'name' => 'Common',  'link' => '?common/index',  'now' => ( $padPage == 'common/index'  ) ? 1 : 0 ],
    [ 'name' => 'Scan',    'link' => '?scan/index',    'now' => ( $padPage == 'scan/index'    ) ? 1 : 0 ]
  ];

  if ( $suiteTest )
    $suites [] = [ 'name' => 'Test', 'link' => $suiteTest, 'now' => 0 ];

?>