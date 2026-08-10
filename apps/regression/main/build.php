<?php

  // The fresh build, at home in this application: clear everything a build regenerates -
  // the reference store, the error dumps, the harvested examples and the suite results -
  // then run the seven suites and the one harvest crawl that remains, which gathers the
  // reference and the examples. The page itself only offers the link; the wipe and the
  // minutes of fetches happen behind go, so a stray click costs nothing.

  if ( isset ( $go ) ) {

    padDeleteDataDir ( DATA . 'reference'  );
    padDeleteDataDir ( DATA . 'regression' );
    padDeleteDataDir ( DATA . 'dumps'      );
    padDeleteDataDir ( DATA . 'temp'       );
    padDeleteDataDir ( DATA . 'examples'   );
    padDeleteDataDir ( DATA . 'suites'     );

    set_time_limit ( 0 );

    getRegression ( '&padExamples&padReference' );

    padRedirect ( 'index' );

  }

  $title = 'Build';

?>