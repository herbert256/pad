<?php

  // The overview the application opens on: one line per suite, each with the totals of
  // its last run. A page load never runs anything; each line links to the page where its
  // own Test lives, and Test here runs all seven suites.

  if ( isset ( $test ) ) {
    getRegression ();
    padRedirect ( $padPage );
  }

  $rows = [];

  foreach ( [ 'Pages' => 'pages', 'Common' => 'common', 'Framework' => 'framework', 'Regression' => 'regression', 'Sequence' => 'sequence', 'Manual' => 'manual', 'Other' => 'other' ] as $suiteName => $suite ) {

    $result = getPages ( $suite );

    $rows [] = [
      'name'   => $suiteName,
      'link'   => "?$suite/index",
      'result' => $result ['summary'],
      'ran'    => $result ['when'] ? date ( 'Y-m-d H:i', $result ['when'] ) : 'never',
      'status' => $result ['failed'] ? 'FAILURES' : ( ( $result ['new'] ?? 0 ) ? 'NEW' : 'ok' )
    ];

  }

  $title = 'Regression';

?>