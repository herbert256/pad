<?php

  // Harness regression: shows the last run of _cases/harness/, and reruns it when Test asks.

  $group = 'harness';
  $intro = 'The runner tested by the runner: the helpers every suite verdict passes through.';

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
  $title   = "Harness regression - $summary";

?>
