<?php

  // Overview of the Framework suite - the sandbox cases run as fetched pages, one request
  // per case, each fetched directly from regression4. Test here reruns the suite; a page load reads the
  // last run, because running means nine hundred requests. The sandbox itself still runs
  // the same cases in-request, one menu entry back.

  if ( isset ( $test ) ) {

    getFrameworkTest ();

    padRedirect ( $padPage );

  }

  $result = getPages ( 'framework' );

  $tests       = $result ['tests'];
  $summary     = $result ['summary'];
  $failedCount = $result ['failed'];

  $verdict = $failedCount ? 'FAILURES' : 'all ok';

  $title   = "Framework regression - $summary";

?>
