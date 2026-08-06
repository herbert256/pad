<?php

  // Escaping regression: runs _cases/escaping/ and reports what differed.

  $group = 'escaping';
  $intro = 'What stops PAD reading braces as tags: the ignore tag, option and pipe.';

  list ( $tests, $summary, $failedCount ) = getCases ( $group );

  $verdict = $failedCount ? 'FAILURES' : 'all ok';
  $title   = "Escaping regression - $summary";

?>
