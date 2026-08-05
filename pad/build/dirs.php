<?php

  // Builds $padBuildDirs: the directory chain from the app root down to the directory the
  // requested page lives in, e.g. APP, APP/abc and APP/abc/klm for page ?abc/klm/page.
  //
  // Every other build step walks this list - _lib is collected along it, _inits.pad and
  // _exits.pad nest along it, _inits.php and _exits.php run along it - so it is what
  // gives subdirectories their inherit-from-parent behaviour.

  $padBuildDirs = [];

  $padBuildNow = substr     ( APP, 0, -1);
  $padBuildPos = strrpos    ( $padBuildNow, '/');
  $padBuildDir = substr     ( $padBuildNow, 0, $padBuildPos);
  $padBuildMrg = substr     ( $padBuildNow, $padBuildPos+1) . '/' . $padDir;
  $padBuildMrg = padExplode ( $padBuildMrg , '/' );

  foreach ( $padBuildMrg as $padV ) {
    $padBuildDir .= "/$padV";
    $padBuildDirs [] = $padBuildDir;
  }

?>