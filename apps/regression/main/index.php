<?php

  // The overview the application opens on: one line per kind of test - the four suites
  // and the scan - each with the totals of its last run. A page load never runs
  // anything; each line links to the page where its own Test lives, and Test here runs
  // everything: the four suites and the scan.

  if ( isset ( $test ) ) {
    getRegression ();
    padRedirect ( $padPage );
  }

  $rows = [];

  foreach ( [ 'Pages' => 'pages', 'Common' => 'common', 'Framework' => 'framework', 'Regression' => 'regression' ] as $suiteName => $suite ) {

    $result = getPages ( $suite );

    $rows [] = [
      'name'   => $suiteName,
      'link'   => "?$suite/index",
      'result' => $result ['summary'],
      'ran'    => $result ['when'] ? date ( 'Y-m-d H:i', $result ['when'] ) : 'never',
      'status' => $result ['failed'] ? 'FAILURES' : ( ( $result ['new'] ?? 0 ) ? 'NEW' : 'ok' )
    ];

  }

  // The scan has no failed count of its own - what it leaves behind is a status per page.
  // All of them are named on the line; the status column reacts to everything that is not
  // ok, expected or random - an undeclared error, a page that went empty, a new page and a
  // warning all demand a look.

  $scanCounts = getScanCounts ();
  $scanTotal  = 0;
  $scanParts  = [];

  foreach ( $scanCounts as $scanStatus => $scanCount ) {
    $scanTotal += $scanCount;
    if ( $scanStatus != 'ok' )
      $scanParts [] = "$scanCount $scanStatus";
  }

  $scanResult = "$scanTotal pages" . ( $scanParts ? ' - ' . implode ( ', ', $scanParts ) : ', all ok' );

  // The scan keeps no run record of its own; every crawl rewrites every status, so the
  // newest of the per-app index statuses is the age of the last one.

  $scanWhen = 0;

  foreach ( glob ( DATA . 'regression/*/index.txt' ) as $scanStamp )
    $scanWhen = max ( $scanWhen, filemtime ( $scanStamp ) );

  $rows [] = [
    'name'   => 'Scan',
    'link'   => '?scan/index',
    'result' => $scanResult,
    'ran'    => $scanWhen ? date ( 'Y-m-d H:i', $scanWhen ) : 'never',
    'status' => ( array_sum ( array_diff_key ( $scanCounts,
                  array_flip ( [ 'ok', 'expected', 'random' ] ) ) ) ) ? 'ATTENTION' : 'ok'
  ];

  $title = 'Regression';

?>