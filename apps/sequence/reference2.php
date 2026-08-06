<?php

  // Cross-reference page: lists the application pages that use one reference item, read from
  // the generated index under DATA/reference/sequence/<dir>/<item>.txt.
  //
  // dir and item arrive from the query string, so the path they build is resolved and checked
  // to still sit inside the reference directory before it is opened. Interpolating them
  // unchecked let a ../ in either one read any .txt file the server could reach, and an
  // unknown value ended the request with a 500 rather than a page. Anything not resolving
  // inside the tree falls back to the default item.

  if ( ! isset ( $type ) ) $type = 'Sequences';
  if ( ! isset ( $dir  ) ) $dir  = 'sequences';
  if ( ! isset ( $item ) ) $item = 'happy';

  $refRoot = realpath ( DATA . 'reference/sequence' );
  $refFile = realpath ( DATA . "reference/sequence/$dir/$item.txt" );

  if ( ! $refRoot or ! $refFile or ! str_starts_with ( $refFile, $refRoot . '/' ) ) {

    $dir     = 'sequences';
    $item    = 'happy';
    $refFile = realpath ( DATA . "reference/sequence/$dir/$item.txt" );

  }

  $go = [];

  if ( $refFile )

    foreach ( file ( $refFile, FILE_IGNORE_NEW_LINES ) as $file ) {

      if ( ! str_contains ( $file, ';' ) )
        continue;

      list ( $app, $page ) = explode ( ';', $file, 2 );

      $go [] = [ 'app' => $app, 'page' => $page ];

    }

  if ( count ( $go ) > 15 )
    $go = array_slice ( $go, 0, 15 );

  $title = "sequences - Xref - $type - $item";

?>