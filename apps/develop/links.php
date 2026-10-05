<?php

  // The output check's link half over every application at once: each literal href, action
  // and src in each template - pages, wrappers, includes, tag templates - judged the way a
  // rendered local page judges its links (padOutputCheckSource in pad/lib/outputCheck.php).
  // A link built from a field or a tag is known only when its page runs; switch on
  // $padCheckOutput, or add &padCheckOutput to a request, to have those checked as well.
  //
  // _common is left out: a ?page written there means the page of whichever application
  // shows it, so it has no single answer.

  $brokenLinks = [];

  $iterator = new RecursiveIteratorIterator ( new RecursiveDirectoryIterator ( APPS, FilesystemIterator::SKIP_DOTS ) );

  foreach ( $iterator as $linksOne ) {

    $linksPath = padCorrectPath ( $linksOne->getPathname () );
    $linksFile = substr ( $linksPath, strlen ( APPS ) );

    if ( str_starts_with ( $linksFile, '_' ) )
      continue;

    if ( ! str_ends_with ( $linksFile, '.pad' ) and ! str_ends_with ( $linksFile, '.html' ) )
      continue;

    $linksApp = padAppBoundary ( $linksFile ) [0];

    if ( ! padAppsListRoot ( $linksApp ) )
      continue;

    foreach ( padOutputCheckSource ( padFileGet ( $linksPath ), $linksApp ) as $linksBroken )
      $brokenLinks [] = [ 'file' => $linksFile, 'broken' => $linksBroken ];

  }

  unset ( $iterator, $linksOne, $linksPath, $linksFile, $linksApp, $linksBroken );

  $brokenLinks = padArrSortBy ( $brokenLinks, 'file' );

  $title = 'Broken links';

?>
