<?php

  // Collects the _lib content of every directory in $padBuildDirs - the _common app first
  // when $padCommon is on - and returns it as one string.
  //
  // Included as `$padBuildLib = include ...`; the per-directory work is in build/lib.php.
  // The returned text is prefixed to the built page, so _lib/*.pad snippets end up in
  // front of the wrapper while _lib/*.php files are pulled in for their functions.

  $padBuildLib = '';

  if ( $padCommon ) {
    $padBuildDir = COMMON;
    include PAD . 'build/lib.php';
  }

  foreach ( $padBuildDirs as $padBuildDir )
    if ( is_dir ("$padBuildDir/_lib") ) 
      include PAD . 'build/lib.php';

  return $padBuildLib;

?>