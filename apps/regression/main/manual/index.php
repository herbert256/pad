<?php

  // Overview of the Manual suite - every page of apps/manual/, fetched over HTTP one
  // request at a time and compared with the prediction of the same name in
  // apps/regression/manual/. The pages stay in their own application; the store holds
  // nothing but what each must answer. Test here reruns this suite; a page load reads the
  // last run.

  getSuitePage ( 'manual' );

?>
