<?php

  // A DEL in the line is written \u007F - in the message as in the context - so the context
  // after the message stays JSON: \x7F, the escape it had, is none, and json_decode gave a
  // syntax error on the line of any value holding one.

  padNowFreeze ( '2001-03-21 04:05:06' );

  $logFile = DATA . "logs/$padApp/2001-03-21.log";

  @unlink ( $logFile );

  padLog ( "probe a\x7Fb", 'info', [ 'v' => "a\x7Fb", 'w' => "c\u{85}d" ] );

  $logLine = trim ( file_get_contents ( $logFile ) );
  $logJson = json_decode ( substr ( $logLine, strpos ( $logLine, '{' ) ), TRUE );
  $logRead = is_array ( $logJson ) ? 'json: ' . json_encode ( array_map ( 'bin2hex', $logJson ) ) : 'no json: ' . json_last_error_msg ();

?>
