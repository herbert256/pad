<?php

  // Functions regression: runs _cases/functions/ and reports what differed.

  $group = 'functions';
  $intro = 'The pipe functions, over the values their reference documents.';

  list ( $tests, $summary, $failedCount ) = getCases ( $group );

  $verdict = $failedCount ? 'FAILURES' : 'all ok';
  $title   = "Functions regression - $summary";

?>
