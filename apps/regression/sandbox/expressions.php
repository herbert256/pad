<?php

  // Expressions regression: shows the last run of _cases/expressions/, and reruns it when Test asks.

  $group = 'expressions';
  $intro = 'Comparison, arithmetic, ranges and the @ placeholder.';

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
  $title   = "Expressions regression - $summary";

?>