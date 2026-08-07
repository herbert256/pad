<?php

  // Custom regression: shows the last run of _cases/custom/, and reruns it when Test asks.

  $group = 'custom';
  $intro = 'The extension points an application supplies: _tags, _functions, _include and _data.';

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
  $title   = "Custom regression - $summary";

?>