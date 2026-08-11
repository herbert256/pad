<?php

  if ( ! isset ( $goBuild ) )
    return;

  include APP . 'clean.php';

  // The reference and the examples are develop's own: the harvest pages beside this one
  // gather them, and they stand between builds. The inner build wipes only what it owns -
  // the suite results and the dumps - and tests against the standing stores.

  // The inner build is thousands of fetches and runs for minutes. This request waits for
  // it, so the script limit and the fetch limit are widened to match - without that, PHP's
  // execution limit terminated this page thirty seconds in and padCurl would have cut the
  // build at its default 120, while the inner request ground on detached, which is what
  // made the build look hung with nothing to show.

  set_time_limit ( 0 );

  padCurl ( $padHost . "regression/main/?build&go=1", [ 'options' => [ 'TIMEOUT' => 3600 ] ] );

  padRestart ( 'errors2' );

?>