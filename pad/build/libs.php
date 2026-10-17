<?php

  // Collects the _lib content of every directory in $padBuildDirs - the _common app first
  // when $padCommon is on - and returns it as one string.
  //
  // Included as `$padBuildLib = include ...`; the per-directory work is in build/lib.php,
  // which also leaves the same text as source-map pieces in $padSrcLib (lib/source.php).
  // The returned text is prefixed to the built page, so _lib/*.pad snippets end up in
  // front of the wrapper while _lib/*.php files are pulled in for their functions.

  $padBuildLib = '';
  $padSrcLib   = [];

  // A _common without a _lib - one holding only _include/ or _tags/, or an empty one beside a
  // project of one's own - is read without it, as the page's directories are: every page
  // died on "RecursiveDirectoryIterator(.../_common/_lib): Failed to open directory".

  if ( $padCommon and is_dir ( COMMON . '_lib' ) ) {
    $padBuildDir = rtrim ( COMMON, '/' );
    include PAD . 'build/lib.php';
  }

  foreach ( $padBuildDirs as $padBuildDir )
    if ( is_dir ("$padBuildDir/_lib") ) 
      include PAD . 'build/lib.php';

  return $padBuildLib;

?>
