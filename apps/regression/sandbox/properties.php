<?php

  // Properties regression: shows the last run of _cases/properties/, and reruns it when Test asks.

  $group = 'properties';
  $intro = 'The property@tag values an iteration publishes about itself.';

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
  $title   = "Properties regression - $summary";

?>