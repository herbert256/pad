<?php

  // Data regression: runs _cases/data/ and reports what differed.

  $group = 'data';
  $intro = 'The data tag over JSON, CSV and XML.';

  list ( $tests, $summary, $failedCount ) = getCases ( $group );

  $verdict = $failedCount ? 'FAILURES' : 'all ok';
  $title   = "Data regression - $summary";

?>
