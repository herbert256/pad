<?php

  // The fresh build, at home in this application: clear everything a build regenerates -
  // the reference store, the crawl baselines, the error dumps, the harvested examples and
  // the suite results - then run the four suites, harvest, crawl, accept and verify
  // through getRegressionBuild(). The page itself only offers the link; the wipe and the
  // minutes of fetches happen behind goBuild, so a stray click costs nothing.
  //
  // The bare ?build&go=1 form stays: develop's build page asks for the build there, doing
  // its own cleaning first.

  if ( isset ( $go ) ) {

    getRegressionBuild ();

    padRedirect ( $padPage );

  }

  if ( isset ( $goBuild ) ) {

    padDeleteDataDir ( DATA . 'reference'  );
    padDeleteDataDir ( DATA . 'regression' );
    padDeleteDataDir ( DATA . 'dumps'      );
    padDeleteDataDir ( DATA . 'temp'       );
    padDeleteDataDir ( DATA . 'examples'   );
    padDeleteDataDir ( DATA . 'suites'     );

    set_time_limit ( 0 );

    getRegressionBuild ();

    padRedirect ( 'index' );

  }

  $title = 'Build';

?>
