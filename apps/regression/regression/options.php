<?php

  // Options regression: shows the last run of _cases/options/, and reruns it when Test asks.

  $group = 'options';
  $intro = 'Tag options, each checked against the same tag without it.';

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
  $title   = "Options regression - $summary";

?>