<?php

  // NAN in the message or in a placeholder's value is written NAN, and INF and NAN in the
  // context as their text: (string) NAN is a PHP 8.5 warning that ended the page a log line
  // must never end, and the JSON of the context wrote them 0.

  padNowFreeze ( '2001-03-23 04:05:06' );

  $logFile = DATA . "logs/$padApp/2001-03-23.log";

  @unlink ( $logFile );

  padLog ( 'ratio {r}', 'debug', [ 'r' => NAN ] );
  padLog ( NAN );
  padLog ( [ 'mean' => NAN, 'list' => [ 1.5, -INF ] ], 'info', [ 'top' => INF, 'deep' => [ 'x' => NAN, 'y' => 0.0 ] ] );

  $logLines = str_replace ( "\n", ' | ', file_get_contents ( $logFile ) );

?>
