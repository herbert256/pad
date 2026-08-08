<?php

  // Overview of the framework regression suites: the totals of every group's last run.
  //
  // Test here reruns all of them, which is the only place that does; each group page under
  // suites/ reruns only its own. A page load never runs anything - it reads what the last run
  // left in DATA, so opening the overview is reading, not testing.

  if ( isset ( $test ) ) {
    getRegressionSandbox ();
    padRedirect ( $padPage );
  }

  $groups   = [];
  $allTotal = 0;
  $allFail  = 0;
  $when     = 0;

  foreach ( getCasesGroups () as $groupName => $groupWhat ) {

    $result = getCases ( $groupName );

    $allTotal += count ( $result ['tests'] );
    $allFail  += $result ['failed'];

    // The oldest of the runs, because that is what the totals can be stale by.

    if ( ! $when or ( $result ['when'] and $result ['when'] < $when ) )
      $when = $result ['when'];

    $groups [] = [
      'group'   => $groupName,
      'what'    => $groupWhat,
      'cases'   => count ( $result ['tests'] ),
      'failed'  => $result ['failed'],
      'ran'     => $result ['when'] ? date ( 'Y-m-d H:i', $result ['when'] ) : 'never',
      'status'  => $result ['failed'] ? 'FAILED' : 'ok'
    ];

  }

  $summary = "$allTotal tests, $allFail failed";
  $verdict = $allFail ? 'FAILURES' : 'all ok';

  $title   = "Framework regression - $summary";

?>