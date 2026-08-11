<?php

  if ( ! isset ( $go ) )
    return;

  // Resets the regression results: clear the suite runs and ask the regression
  // application for a fresh Test of all seven.

  padDeleteDataDir ( DATA . 'suites' );

  set_time_limit ( 0 );

  padCurl ( $padHost . 'regression/main/?index&test', [ 'options' => [ 'TIMEOUT' => 3600 ] ] );

?>
