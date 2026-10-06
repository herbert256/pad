<?php

  // Lists the error dumps a crawl left behind, which errors.php and build.php restart on once
  // they have finished crawling.
  //
  // DATA/dumps only exists once something has dumped into it. Both of those pages delete it
  // before they start, so a run that raised no error at all leaves nothing here - and this page
  // ended the request on the directory being missing, which made a clean build look like a
  // broken one. Nothing to list is the good outcome, not a failure.

  $list = [];

  if ( is_dir ( DATA . 'dumps' ) ) {

    $dir = new RecursiveDirectoryIterator ( DATA . 'dumps', FilesystemIterator::SKIP_DOTS );
    $dir = new RecursiveIteratorIterator  ( $dir );

    foreach ( $dir as $one ) {

      if ( ! $one->isFile() or $one->getFilename() [0] == '.' )
        continue;

      // One line per dump, read from its _ERROR.html - the -11 below cuts that name off to
      // link the dump's directory. Without the test every file of every dump was listed.

      if ( ! str_ends_with ( $one->getFilename(), '_ERROR.html' ) )
        continue;

      $path = padCorrectPath ( $one->getPathname() );

      $list [] = [ 'url' => substr ( str_replace ( DATA , '', $path ), 0, -11),
                   'txt' => padFileGet ($path) ];

    }

  }

?>
