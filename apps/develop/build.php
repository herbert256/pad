<?php

  if ( ! isset ( $goBuild ) )
    return;

  include APP . 'clean.php';

  padDeleteDataDir ( DATA . 'reference'  );
  padDeleteDataDir ( DATA . 'regression' );
  padDeleteDataDir ( DATA . 'dumps'      );
  padDeleteDataDir ( DATA . 'temp'       );
  padDeleteDataDir ( DATA . 'examples'   );
  padDeleteDataDir ( DATA . 'suites'     );

  // The inner build is thousands of fetches and runs for minutes. This request waits for
  // it, so the script limit and the fetch limit are widened to match - without that, PHP's
  // execution limit terminated this page thirty seconds in and padCurl would have cut the
  // build at its default 120, while the inner request ground on detached, which is what
  // made the build look hung with nothing to show.

  set_time_limit ( 0 );

  padCurl ( $padHost . "regression/?build&go=1", [ 'options' => [ 'TIMEOUT' => 3600 ] ] );

  padRestart ( 'errors2' );

?>