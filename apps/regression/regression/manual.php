<?php

  // Manual regression: shows the last run of _cases/manual/, and reruns it when Test asks.

  $group = 'manual';
  $intro = 'The examples the manual embeds with {example}, rather than the prose pages around them.';

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
  $title   = "Manual regression - $summary";

?>