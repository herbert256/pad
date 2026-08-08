<?php

  // Data regression: shows the last run of _cases/data/, and reruns it when Test asks.

  $group = 'data';
  $intro = 'The data tag over JSON, CSV and XML.';

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
  $title   = "Data regression - $summary";

?>