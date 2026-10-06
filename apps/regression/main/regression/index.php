<?php

  // Overview of the Regression suite - the self-testing applications under regression/,
  // every page fetched over HTTP one request at a time and compared with the prediction of
  // the same name in apps/regression/regression/. The pages stay in their own applications;
  // the store holds nothing but what each must answer. Test here reruns this suite; a page
  // load reads the last run.

  getSuitePage ( 'regression' );

?>
