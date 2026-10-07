<?php

  // The whole line is escaped, the context written after the message as much as the
  // message, and the C1 controls with it: a NEL in a context value - written raw by the
  // JSON of the context - started a forged line for Python's splitlines, and a CSI
  // (U+009B, a terminal's escape and [ in one character) passed raw in message and context.

  padNowFreeze ( '2001-03-19 04:05:06' );

  $logFile = DATA . "logs/$padApp/2001-03-19.log";

  @unlink ( $logFile );

  padLog ( 'user {name} logged in', 'info', [ 'name' => "eve\u{85}2001-03-19 04:05:07 INFO admin logged in" ] );
  padLog ( "a\u{9B}2Kb", 'info', [ 'note' => "c\u{9B}1Ad" ] );

  $logText  = file_get_contents ( $logFile );
  $logCount = count ( file ( $logFile ) );
  $logRaw   = preg_match ( '/\xC2[\x80-\x9F]/', $logText ) ? 'raw C1 left' : 'no raw C1';
  $logLines = str_replace ( "\n", ' | ', $logText );

?>
