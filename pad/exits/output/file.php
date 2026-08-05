<?php

  // File writer: writes the page to disk instead of sending it, under the name built from
  // the $padFile* settings in config/output/file.php.
  //
  // Output then switches back to 'web' and the request restarts on $padFileNextPage, so
  // the visitor still gets a normal page telling them the file was written.

  padFilePut ( padFileName (), $padOutput );

  $padSetConfig ['OutputType'] = 'web';

  $padRestart = $padFileNextPage;
  include PAD . 'start/restart.php';

?>