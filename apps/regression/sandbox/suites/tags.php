<?php

  // Tags regression: shows the last run of _cases/tags/, and reruns it when Test asks.

  $group = 'tags';
  $intro = 'The tags that decide what is rendered and how often, and what is put in the page.';

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
  $title   = "Tags regression - $summary";

?>