<?php

  // The eight PSR-3 levels, debug to emergency, written in any case and with spaces
  // around them.

  padNowFreeze ( '2001-02-06 04:05:06' );

  $logFile = DATA . "logs/$padApp/2001-02-06.log";

  @unlink ( $logFile );

  foreach ( [ 'debug', 'Info', 'NOTICE', ' warning ', 'error', 'critical', 'alert', 'emergency' ] as $level )
    padLog ( 'x', $level );

  $logLevels = implode ( ' ', array_map ( fn ( $line ) => explode ( ' ', $line ) [2], file ( $logFile, FILE_IGNORE_NEW_LINES ) ) );

?>
