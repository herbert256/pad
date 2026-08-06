<?php

  // Overview of the framework regression suites: runs every group and reports the totals.
  //
  // The pages beside this one each hold one group and show it case by case; this one is the
  // single place to look to see whether anything moved. It runs the same getCases() they do,
  // so a total here can never disagree with the page it links to.

  $title = 'Framework regression';

  $groups   = [];
  $allTotal = 0;
  $allFail  = 0;

  foreach ( getCasesGroups () as $groupName => $groupWhat ) {

    list ( $groupTests, $groupSummary, $groupFailed ) = getCases ( $groupName );

    $allTotal += count ( $groupTests );
    $allFail  += $groupFailed;

    $groups [] = [
      'group'   => $groupName,
      'what'    => $groupWhat,
      'cases'   => count ( $groupTests ),
      'failed'  => $groupFailed,
      'status'  => $groupFailed ? 'FAILED' : 'ok'
    ];

  }

  $summary = "$allTotal tests, $allFail failed";
  $verdict = $allFail ? 'FAILURES' : 'all ok';

  $title   = "Framework regression - $summary";

?>
