<?php

  // Pulls in one directory's _lib tree, recursively: .php files are included once, so
  // their functions become available to the page, .pad files are read as template text.
  //
  // Called per directory from build/libs.php with $padBuildDir set; everything it
  // produces is appended to $padBuildLib.

  foreach ( padDirFiles ( "$padBuildDir/_lib" ) as $padCall ) {

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

?>
