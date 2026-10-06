<?php

  // padFilePut with an empty string empties the file it overwrites - it used to keep the old
  // contents and answer TRUE all the same - while an empty append and NULL leave it alone.

  $emptyFile = 'temp/a_file_written_empty_is_empty.txt';

  padFilePut ( $emptyFile, 'before' );
  padFilePut ( $emptyFile, '' );

  clearstatcache ();
  $emptySize = filesize ( DATA . $emptyFile );

  padFilePut ( $emptyFile, 'kept' );
  padFilePut ( $emptyFile, '', 1 );
  padFilePut ( $emptyFile, NULL );

  $emptyKept = file_get_contents ( DATA . $emptyFile );

  unlink ( DATA . $emptyFile );

?>
