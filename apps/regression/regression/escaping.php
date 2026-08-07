<?php

  // Escaping regression: shows the last run of _cases/escaping/, and reruns it when Test asks.

  $group = 'escaping';
  $intro = 'What stops PAD reading braces as tags: the ignore tag, option and pipe.';

  if ( isset ( $test ) ) {
    getCasesTest ( $group );
    padRedirect ( $padPage );
  }

  $result = getCases ( $group );

  $tests       = $result ['tests'];
  $summary     = $result ['summary'];
  $failedCount = $result ['failed'];
  $when        = $result ['when'];

  $verdict = $failedCount ? 'FAILURES' : 'all ok';
  $title   = "Escaping regression - $summary";

?>