<?php

  // Variables regression: runs _cases/variables/ and reports what differed.

  $group = 'variables';
  $intro = 'Assignment, scope, and what a name resolves to.';

  list ( $tests, $summary, $failedCount ) = getCases ( $group );

  $verdict = $failedCount ? 'FAILURES' : 'all ok';
  $title   = "Variables regression - $summary";

?>
