<?php

  // The fresh build: clear the error dumps and the suite results, then run the seven
  // suites against the standing reference and examples stores - those belong to develop,
  // which gathers them with its own harvest pages. The page itself only offers the link;
  // the wipe and the minutes of fetches happen behind go, so a stray click costs nothing.

  if ( isset ( $go ) ) {

    padDeleteDataDir ( DATA . 'dumps'  );
    padDeleteDataDir ( DATA . 'temp'   );
    padDeleteDataDir ( DATA . 'suites' );

    set_time_limit ( 0 );

    getRegression ();

    padRedirect ( 'index' );

  }

  $title = 'Build';

?>