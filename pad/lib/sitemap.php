<?php

  // The pages of the sitemap, generated from the file tree - the part of {sitemap} that a
  // page's PHP uses too: padSitemapPages, the rows of every page, or of the pages below a
  // directory. The walk itself, the XML sitemap and the robots.txt are tags/_lib/sitemap.php,
  // which this function reads when it runs.

  // A directory that holds a _guard.php is left out with everything below it - the walk
  // passes over such a subdirectory, and the same holds for the application root and for
  // every directory down to the one asked for, which the walk starts below: an application
  // guarded at its root listed every page its guard stands over, in a sitemap.xml that is
  // answered before any guard runs, and {sitemap 'admin'} listed admin/'s guarded pages.

  function padSitemapPages ( $dir = '' ) {

    include_once PAD . 'tags/_lib/sitemap.php';

    $dir   = trim ( (string) $dir, '/' );
    $check = APP;

    foreach ( array_merge ( [ '' ], $dir === '' ? [] : explode ( '/', $dir ) ) as $part ) {
      $check .= ( $part === '' ) ? '' : "$part/";
      if ( file_exists ( $check . '_guard.php' ) )
        return [];
    }

    return padSitemapWalk ( $dir === '' ? '' : "$dir/" );

  }

?>
