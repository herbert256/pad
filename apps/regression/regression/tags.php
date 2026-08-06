<?php

  // Tags regression: runs _cases/tags/ and reports what differed.

  $group = 'tags';
  $intro = 'The tags that decide what is rendered and how often, and what is put in the page.';

  list ( $tests, $summary, $failedCount ) = getCases ( $group );

  $verdict = $failedCount ? 'FAILURES' : 'all ok';
  $title   = "Tags regression - $summary";

?>
