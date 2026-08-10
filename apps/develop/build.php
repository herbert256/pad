<?php

  if ( ! isset ( $goBuild ) )
    return;

  include APP . 'clean.php';

  // The wiping of what a build regenerates moved home to the regression application -
  // ?build&go=1 clears the stores itself before it builds, so only the source-tree
  // trimming above is develop's own.

  // The inner build is thousands of fetches and runs for minutes. This request waits for
  // it, so the script limit and the fetch limit are widened to match - without that, PHP's
  // execution limit terminated this page thirty seconds in and padCurl would have cut the
  // build at its default 120, while the inner request ground on detached, which is what
  // made the build look hung with nothing to show.

  set_time_limit ( 0 );

  padCurl ( $padHost . "regression/main/?build&go=1", [ 'options' => [ 'TIMEOUT' => 3600 ] ] );

  padRestart ( 'errors2' );

?>