<?php

  // Variables regression: shows the last run of _cases/variables/, and reruns it when Test asks.

  $group = 'variables';
  $intro = 'Assignment, scope, and what a name resolves to.';

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
  $title   = "Variables regression - $summary";

?>