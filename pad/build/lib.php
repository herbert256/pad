<?php

  // Pulls in one directory's _lib tree, recursively: .php files are included once, so
  // their functions become available to the page, .pad files are read as template text.
  //
  // Called per directory from build/libs.php with $padBuildDir set; everything it
  // produces is appended to $padBuildLib.

  $padLibDirectory = new RecursiveDirectoryIterator ("$padBuildDir/_lib");
  $padLibIterator  = new RecursiveIteratorIterator  ($padLibDirectory);

  foreach ( $padLibIterator as $padLibOne ) {

    $padCall = $padLibOne->getPathname();

    // Each part also goes into the source map's pieces: a .pad file as itself, what a .php
    // file prints as text no template holds.

    if ( substr($padCall, -4) == '.php' ) {
      $padLibText    = include PAD . 'call/once.php';
      $padBuildLib  .= $padLibText;
      $padSrcLib []  = [ $padLibText, '', 0 ];
    }

    if ( substr($padCall, -4) == '.pad' ) {
      $padLibText    = padFileGet ( $padCall );
      $padBuildLib  .= $padLibText;
      $padSrcLib []  = [ $padLibText, $padCall, 0 ];
    }

  }

  // The two iterators hold an open directory handle each, and this file is included once per
  // directory of the chain - so a page built inside another page opens them all over again while
  // the outer set is still held. Nine deep, as the {page} chains are, that was enough to run out
  // of handles: roughly one request in ten died on "Failed to open directory" for a _lib that was
  // plainly there, and only ever on a page that nests. Letting them go here rather than at the
  // end of the request costs nothing and keeps the count flat.

  unset ( $padLibIterator, $padLibDirectory );

?>
