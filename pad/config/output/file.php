<?php

  // Settings for $padOutputType 'file', where the rendered page is written to disk instead
  // of being sent to the client.
  //
  // The first six values are the recipe padFileName() in lib/paths.php uses to build the
  // target path - directory, base name, extension, and whether to append a date, a timestamp
  // and a random id of the given length. $padFileNextPage is the page exits/output/file.php
  // restarts the request with once the file is written. Also included by download.php, which
  // reuses the same naming scheme.

  $padFileName       = 'name';
  $padFileDir        = 'dir';
  $padFileExtension  = 'ext';
  $padFileDate       = FALSE;
  $padFileTimeStamp  = TRUE;
  $padFileUniqId     = 12;
  $padFileNextPage   = 'file/done';

?>