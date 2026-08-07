<?php

  // File writer: writes the page to disk instead of sending it, under the name built from
  // the $padFile* settings in config/output/file.php.
  //
  // Output then switches back to 'web' and the request restarts on $padFileNextPage, so
  // the visitor still gets a normal page telling them the file was written.
  //
  // The name is kept in $padFile, which is what that next page reads to say which file it was -
  // check/file/done has been printing {$padFile} since it was written, and getting the fallback
  // its own .php sets, because this file computed the name and dropped it. padFileName() is
  // called once for the same reason: it ends in padRandomString(), so a second call would name a
  // different file. exits/output/download.php keeps it the same way.

  $padFile = padFileName ();

  padFilePut ( $padFile, $padOutput );

  $padSetConfig ['OutputType'] = 'web';

  $padRestart = $padFileNextPage;
  include PAD . 'start/restart.php';

?>