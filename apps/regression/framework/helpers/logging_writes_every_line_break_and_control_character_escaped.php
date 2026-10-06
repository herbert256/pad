<?php

  // One call is one line for every reader of the log: besides CR and LF, a vertical tab, a
  // form feed, NEL, the Unicode line and paragraph separators - lines to Python's
  // splitlines and to editors - and the escape that starts a terminal's control sequence
  // are written as escapes. A visitor's value with \e[1A\e[2K in it wiped the line above it
  // from the screen of whoever read the log with cat or tail, and one with   started a
  // line of its own there.

  padNowFreeze ( '2001-03-17 04:05:06' );

  $logFile = DATA . "logs/$padApp/2001-03-17.log";

  @unlink ( $logFile );

  padLog ( "a\x0Bb\x0Cc\u{85}d\u{2028}e\u{2029}f\x1B[2Kg\x00h\x7Fi\tj\nk", 'warning' );
  padLog ( 'user {name} logged in', 'info', [ 'name' => "eve\u{2028}2001-03-17 04:05:07 INFO admin logged in" ] );

  $logCount = count ( file ( $logFile ) );
  $logLines = str_replace ( "\n", ' | ', file_get_contents ( $logFile ) );

?>
