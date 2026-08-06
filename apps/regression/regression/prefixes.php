<?php

  // Prefixes regression: runs _cases/prefixes/ and reports what differed.

  $group = 'prefixes';
  $intro = 'The type prefixes that say what a name means.';

  list ( $tests, $summary, $failedCount ) = getCases ( $group );

  $verdict = $failedCount ? 'FAILURES' : 'all ok';
  $title   = "Prefixes regression - $summary";

?>
