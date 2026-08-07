<?php

  // Check regression: shows the last run of _cases/check/, and reruns it when Test asks.

  $group = 'check';
  $intro = 'Pages carried over from the check application, which renders them but asserts nothing.';

  if ( isset ( $test ) ) {
    getCasesTest ( $group );
    padRedirect ( $padPage );
  }

  $result = getCases ( $group );

  $tests       = $result ['tests'];
  $summary     = $result ['summary'];
  $failedCount = $result ['failed'];
  $when        = $result ['when'];

  $verdict = $failedCount ? 'FAILURES' : 'all ok';
  $title   = "Check regression - $summary";

?>