<?php

  // Expressions regression: runs _cases/expressions/ and reports what differed.

  $group = 'expressions';
  $intro = 'Comparison, arithmetic, ranges and the @ placeholder.';

  list ( $tests, $summary, $failedCount ) = getCases ( $group );

  $verdict = $failedCount ? 'FAILURES' : 'all ok';
  $title   = "Expressions regression - $summary";

?>
