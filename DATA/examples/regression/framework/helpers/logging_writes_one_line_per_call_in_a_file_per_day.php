<?php

  // padLog appends to DATA/logs/<application>/<Y-m-d>.log: the time, the level in capitals,
  // the message and the context as JSON, one line per call. The time is padNow's, so a
  // frozen clock makes the line the same on every run. info is the default level; the
  // answer is TRUE.

  padNowFreeze ( '2001-02-03 04:05:06' );

  $logFile = DATA . "logs/$padApp/2001-02-03.log";

  @unlink ( $logFile );

  $logAnswers = json_encode ( [ padLog ( 'Order paid' ),
                                padLog ( 'Disk almost full', 'warning', [ 'free' => '2%', 'disk' => '/data' ] ),
                                padLog ( 'No context', 'debug', [] ) ] );

  $logLines = str_replace ( "\n", ' | ', file_get_contents ( $logFile ) );
  $logMode  = substr ( sprintf ( '%o', fileperms ( $logFile ) ), -3 );

?>
