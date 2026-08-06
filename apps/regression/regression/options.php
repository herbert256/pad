<?php

  // Options regression: runs _cases/options/ and reports what differed.

  $group = 'options';
  $intro = 'Tag options, each checked against the same tag without it.';

  list ( $tests, $summary, $failedCount ) = getCases ( $group );

  $verdict = $failedCount ? 'FAILURES' : 'all ok';
  $title   = "Options regression - $summary";

?>
