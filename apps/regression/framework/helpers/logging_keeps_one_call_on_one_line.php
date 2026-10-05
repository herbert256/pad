<?php

  // A newline or carriage return in the message is written \n or \r, so one call is one
  // line; a message that is no text is JSON, NULL nothing, a boolean true or false, and a
  // NULL context is no context.

  padNowFreeze ( '2001-02-05 04:05:06' );

  $logFile = DATA . "logs/$padApp/2001-02-05.log";

  @unlink ( $logFile );

  padLog ( "two\nlines\r\nand more", 'notice' );
  padLog ( [ 'id' => 7, 'tags' => [ 'x', 'y' ] ], 'info', NULL );
  padLog ( NULL );
  padLog ( TRUE );
  padLog ( 12.5 );

  $logCount = count ( file ( $logFile ) );
  $logLines = str_replace ( "\n", ' | ', file_get_contents ( $logFile ) );

?>
