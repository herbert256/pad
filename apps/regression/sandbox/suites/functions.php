<?php

  // Functions regression: shows the last run of _cases/functions/, and reruns it when Test asks.

  $group = 'functions';
  $intro = 'The pipe functions, over the values their reference documents.';

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
  $title   = "Functions regression - $summary";

?>