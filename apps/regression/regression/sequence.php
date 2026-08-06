<?php

  // Sequence regression: runs _cases/sequence/ and reports what differed.
  //
  // The 'scope' cases in library.php read $seqFixture, which _lib/cases.php declares.

  $group = 'sequence';
  $intro = 'The sequence subsystem: types, actions, plays, stores, options and membership.';

  list ( $tests, $summary, $failedCount ) = getCases ( $group );

  $verdict = $failedCount ? 'FAILURES' : 'all ok';
  $title   = "Sequence regression - $summary";

?>
