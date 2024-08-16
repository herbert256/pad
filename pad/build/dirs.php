<?php

  $padBuildDirs = [];

  $padBuildNow = substr     ( '/app/', 0, -1);  
  $padBuildPos = strrpos    ( $padBuildNow, '/');  
  $padBuildDir = substr     ( $padBuildNow, 0, $padBuildPos);  
  $padBuildMrg = substr     ( $padBuildNow, $padBuildPos+1) . '/' . $padDir; 
  $padBuildMrg = padExplode ( $padBuildMrg , '/' );

  foreach ( $padBuildMrg as $padV ) {
    $padBuildDir .= "/$padV";
    $padBuildDirs [] = $padBuildDir;
  }

?>