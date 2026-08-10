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

    $dir = new RecursiveDirectoryIterator ( DATA . 'dumps' );
    $dir = new RecursiveIteratorIterator  ( $dir );

    foreach ( $dir as $one ) {

      $path = padCorrectPath ( $one->getPathname() );

      if ( ! str_ends_with( $path, '_ERROR.html' ) ) continue;
      if ( str_contains ( $path, '/error' )        ) continue;
      if ( str_contains ( $path, '/support/' )     ) continue;

      // The error applications exist to fail: their boom probes dump on every crawl by
      // design, and a build that worked is not the page to shout about them.

      if ( str_contains ( $path, 'dumps/regression_error_' ) ) continue;

      $list [] = [ 'url' => substr ( str_replace ( DATA , '', $path ), 0, -11),
                   'txt' => padFileGet ($path) ];

    }

  }

?>