<?php

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