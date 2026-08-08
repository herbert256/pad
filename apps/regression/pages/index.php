<?php

  // Overview of the Pages suite - the regression2 tests, the pages that run with _common
  // switched off. Every test is fetched over HTTP, against the outcome recorded beside it.
  // Test here reruns this suite; a page load reads the last run, because running means one
  // request per test. The pages that use _common are the Common suite, one menu entry along.

  if ( isset ( $test ) ) {

    getPagesTest ( 'pages' );

    padRedirect ( $padPage );

  }

  $result = getPages ( 'pages' );

  $tests       = $result ['tests'];
  $summary     = $result ['summary'];
  $failedCount = $result ['failed'];

  $verdict = $failedCount ? 'FAILURES' : 'all ok';

  $title   = "Pages regression - $summary";

?>