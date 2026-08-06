<?php

  // Properties regression: runs _cases/properties/ and reports what differed.

  $group = 'properties';
  $intro = 'The property@tag values an iteration publishes about itself.';

  list ( $tests, $summary, $failedCount ) = getCases ( $group );

  $verdict = $failedCount ? 'FAILURES' : 'all ok';
  $title   = "Properties regression - $summary";

?>
