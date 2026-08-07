<?php

  // Overview of the pages suite: every test fetched over HTTP, against the outcome recorded
  // beside it. Test here reruns them all; a page load reads the last run, because running means
  // one request per test.

  if ( isset ( $test ) ) {

    getRegressionPages ();

    padRedirect ( $padPage );

  }

  $result = getPages ();

  $tests       = $result ['tests'];
  $summary     = $result ['summary'];
  $failedCount = $result ['failed'];

  $verdict = $failedCount ? 'FAILURES' : 'all ok';

  $title   = "Pages regression - $summary";

?>