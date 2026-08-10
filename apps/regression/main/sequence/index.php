<?php

  // Overview of the Sequence suite - every page of apps/sequence/, fetched over HTTP one
  // request at a time and compared with the prediction of the same name in
  // apps/regression/sequence/. The pages stay in their own application; the store holds
  // nothing but what each must answer. Test here reruns this suite; a page load reads the
  // last run.

  if ( isset ( $test ) ) {

    getPagesTest ( 'sequence' );

    padRedirect ( $padPage );

  }

  $result = getPages ( 'sequence' );

  $tests       = $result ['tests'];
  $summary     = $result ['summary'];
  $failedCount = $result ['failed'];

  $verdict = $failedCount ? 'FAILURES' : 'all ok';

  $title   = "Sequence suite - $summary";

?>
