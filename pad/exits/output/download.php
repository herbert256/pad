<?php

  // Download writer: sends the page as a file attachment - download headers carrying the
  // generated file name, content type and length, then the body - and exits.

  $padFile = padFileName ( FALSE );

  padDownLoadHeaders ( $padContentType, $padFile, $padLen );

  echo $padOutput;

  padExit ();

?>