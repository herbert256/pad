<?php

  // The overview the application opens on: one line per kind of test - the three pages
  // suites and the scan - each with the totals of its last run. A page load never runs
  // anything; each line links to the page where its own Test lives, and Test here runs
  // everything: the three suites and the scan.

  if ( isset ( $test ) ) {
    getRegression ();
    padRedirect ( $padPage );
  }

  $rows = [];

  foreach ( [ 'Pages' => 'pages', 'Common' => 'common', 'Framework' => 'framework' ] as $suiteName => $suite ) {

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

  // The scan keeps no run record of its own; the status of its own overview is rewritten on
  // every crawl, so that file's age is the age of the last one.

  $scanStamp = DATA . 'regression/regression/main/scan/index.txt';
  $scanWhen  = file_exists ( $scanStamp ) ? filemtime ( $scanStamp ) : 0;

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