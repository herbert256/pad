<?php

  // Passes regression: shows the last run of _cases/passes/, and reruns it when Test asks.

  $group = 'passes';
  $intro = 'Nested passes: the four isolation forms, and what may and may not cross their boundary.';

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
  $title   = "Passes regression - $summary";

?>
