<?php

  // {name} in the message is filled from the context (PSR-3): text as it is, NULL as
  // nothing, a boolean as true or false, a moment as its date and time, an array as JSON,
  // a Throwable as its class and message. A name the context lacks stays as written. The
  // whole context still follows, a Throwable in it as class, message, file and line.

  padNowFreeze ( '2001-02-04 04:05:06' );

  $logFile = DATA . "logs/$padApp/2001-02-04.log";

  @unlink ( $logFile );

  padLog ( 'Order {order} paid by {user.name}{nothing} {missing} {paid} {when} {items}', 'info',
           [ 'order' => 42, 'user.name' => 'Zoë', 'nothing' => NULL, 'paid' => FALSE,
             'when' => new DateTime ( '2020-01-01 12:00:00' ), 'items' => [ 'a' => 1 ] ] );

  padLog ( 'Import failed: {exception}', 'error', [ 'exception' => new RuntimeException ( 'no file' ) ] );

  $logLines = file ( $logFile, FILE_IGNORE_NEW_LINES );
  $logFirst = $logLines [0];
  $logError = preg_replace ( '/"file":"[^"]*","line":\d+/', '"file":"...","line":N', $logLines [1] );

?>
