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

    if ( substr($padCall, -4) == '.php' )
      $padBuildLib .= include PAD . 'call/once.php';

    if ( substr($padCall, -4) == '.pad' )
      $padBuildLib .= padFileGet ( $padCall );

  }

?>