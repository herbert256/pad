<?php

  // The overview the application opens on: one line per suite, each with the totals of
  // its last run. A page load never runs anything; each line links to the page where its
  // own Test lives, and Test here runs all eight suites.

  if ( isset ( $test ) ) {
    getRegression ();
    padRedirect ( $padPage );
  }

  $rows = [];

  foreach ( array_keys ( getSuites () ) as $suite ) {

    $result = getSuiteLast ( $suite );

    $rows [] = [
      'name'   => ucfirst ( $suite ),
      'link'   => "?$suite/index",
      'result' => $result ['summary'],
      'ran'    => $result ['when'] ? date ( 'Y-m-d H:i', $result ['when'] ) : 'never',
      'status' => $result ['failed'] ? 'FAILURES' : ( ( $result ['new'] ?? 0 ) ? 'NEW' : 'ok' )
    ];

  }

  $title = 'Regression';

?>