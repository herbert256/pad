<?php

  // A relative path is a DATA path to padFileGet as it is to padFilePut - the get took it
  // under the engine's own directory, so it read another file, here none at all.

  $backFile = 'temp/a_file_put_is_read_back_by_the_same_relative_name.txt';

  padFilePut ( $backFile, 'written' );

  $backRead = padFileGet ( $backFile, 'missing' );

  unlink ( DATA . $backFile );

?>
