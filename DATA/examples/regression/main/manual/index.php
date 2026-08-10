<?php

  // Overview of the Manual suite - every page of apps/manual/, fetched over HTTP one
  // request at a time and compared with the prediction of the same name in
  // apps/regression/manual/. The pages stay in their own application; the store holds
  // nothing but what each must answer. Test here reruns this suite; a page load reads the
  // last run.

  if ( isset ( $test ) ) {

    getPagesTest ( 'manual' );

    padRedirect ( $padPage );

  }

  $result = getPages ( 'manual' );

  $tests       = $result ['tests'];
  $summary     = $result ['summary'];
  $failedCount = $result ['failed'];

  $verdict = $failedCount ? 'FAILURES' : 'all ok';

  $title   = "Manual suite - $summary";

?>
