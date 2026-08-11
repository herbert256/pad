<?php

  // The fresh build, at home in this application: clear everything a build regenerates -
  // the reference store, the error dumps, the harvested examples and the suite results -
  // then run the seven suites and the one harvest crawl that remains, which gathers the
  // reference and the examples. The page itself only offers the link; the wipe and the
  // minutes of fetches happen behind go, so a stray click costs nothing.

  if ( isset ( $go ) ) {

    padDeleteDataDir ( DATA . 'dumps'  );
    padDeleteDataDir ( DATA . 'temp'   );
    padDeleteDataDir ( DATA . 'suites' );

    set_time_limit ( 0 );

    // The harvest lives in develop, which owns the reference and the examples; it runs
    // first because the reference and manual applications render from what it gathers,
    // and the suites must see the store a build actually leaves.

    padCurl ( $padHost . 'develop/?harvest&go=1', [ 'options' => [ 'TIMEOUT' => 3600 ] ] );

    getRegression ();

    padRedirect ( 'index' );

  }

  $title = 'Build';

?>