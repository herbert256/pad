<?php

  // Three lines in today's log of regression/sitemap - an application whose name holds a
  // slash, its log in DATA/logs/regression/sitemap/: the dashboard counted none of them.

  $logFile   = DATA . 'logs/regression/sitemap/' . date ( 'Y-m-d' ) . '.log';
  $logHad    = is_file ( $logFile );
  $logBefore = adminLogToday ();

  @mkdir ( dirname ( $logFile ), 0777, TRUE );
  file_put_contents ( $logFile, "one\ntwo\nthree\n", FILE_APPEND );

  $logAdded = adminLogToday () - $logBefore;

  if ( ! $logHad ) {
    unlink ( $logFile );
    @rmdir ( dirname ( $logFile ) );
  }

?>
