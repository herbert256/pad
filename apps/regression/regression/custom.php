<?php

  // Custom regression: runs _cases/custom/ and reports what differed.

  $group = 'custom';
  $intro = 'The extension points an application supplies: _tags, _functions, _include and _data.';

  list ( $tests, $summary, $failedCount ) = getCases ( $group );

  $verdict = $failedCount ? 'FAILURES' : 'all ok';
  $title   = "Custom regression - $summary";

?>
