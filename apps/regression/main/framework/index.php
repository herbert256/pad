<?php

  // Overview of the Framework suite - the engine cases as fetched pages, one request per
  // case, each fetched directly from regression/framework. Test here reruns the suite; a page load
  // reads the last run, because running means nine hundred requests.

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