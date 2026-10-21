<?php

  // Returns the source of the app page named $padGetName, loaded by get/go/call.php.
  // Lets a page be pulled in as content without going through a request of its own.

  $padGetPage = padAppPageCheck ( $padGetName );

  // The page's directory guards decide first, as they do for a {page} (build/guards.php):
  // a refusal renders it as nothing. content='admin/report' ran and showed a page below a
  // _guard.php that refused it to the same visitor. The guards ask $padPage and $padDir,
  // so for the walk they are the pulled page's, and the request's again after it.

  $padGetKeep = [ $padPage, $padDir, $padPath, $padBuildDirs ?? [] ];

  $padPage = ltrim ( $padGetPage, '/' );
  $padDir  = padDir ();
  $padPath = padPath ();

  include PAD . 'build/dirs.php';
  include PAD . 'build/guards.php';

  [ $padPage, $padDir, $padPath, $padBuildDirs ] = $padGetKeep;

  if ( $padBuildRefused )
    return '';

  $padGetCall = APP2 . $padGetPage;

  return include PAD . 'get/go/call.php';

?>
