<?php

  // Prefixes regression: shows the last run of _cases/prefixes/, and reruns it when Test asks.

  $group = 'prefixes';
  $intro = 'The type prefixes that say what a name means.';

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
  $title   = "Prefixes regression - $summary";

?>