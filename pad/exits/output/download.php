<?php

  // Download writer: sends the page as a file attachment - download headers carrying the
  // generated file name, content type and length, then the body - and exits.

  $padFile = padFileName ( FALSE );

  padDownLoadHeaders ( $padContentType, $padFile, $padLen );

  echo $padOutput;

  // Once the file is out nothing more may be written, as padWebSend has it for a page: the
  // download said neither that it had gone (padSent) nor turned PHP's display of errors
  // off, and a late error - a shutdown function's warning, a padError - landed behind the
  // Content-Length just sent.

  $padSent = TRUE;

  ini_set ( 'display_errors', '0' );

  padExit ();

?>
