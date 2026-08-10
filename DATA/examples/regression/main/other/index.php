<?php

  // Overview of the Other suite - every page of the applications without a suite of their own, fetched over HTTP one
  // request at a time and compared with the prediction of the same name in
  // apps/regression/other/. The pages stay in their own application; the store holds
  // nothing but what each must answer. Test here reruns this suite; a page load reads the
  // last run.

  if ( isset ( $test ) ) {

    getPagesTest ( 'other' );

    padRedirect ( $padPage );

  }

  $result = getPages ( 'other' );

  $tests       = $result ['tests'];
  $summary     = $result ['summary'];
  $failedCount = $result ['failed'];

  $verdict = $failedCount ? 'FAILURES' : 'all ok';

  $title   = "Other suite - $summary";

?>
