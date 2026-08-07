<?php

  // Sequence regression: shows the last run of _cases/sequence/, and reruns it when Test asks.

  $group = 'sequence';
  $intro = 'The sequence subsystem: types, actions, plays, stores, options and membership.';

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
  $title   = "Sequence regression - $summary";

?>