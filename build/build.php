<?php

  $padBuildModeSave = $padBuildMode;

  if     ( isset ( $padIncludeSet          ) ) $padInclude = $padIncludeSet;
  elseif ( isset ( $_REQUEST['padInclude'] ) ) $padInclude = TRUE;

  $padBuildMode  = $padBuildModeSet  ?? $_REQUEST['padBuildMode']  ?? $padBuildMode;
  $padBuildMerge = $padBuildMergeSet ?? $_REQUEST['padBuildMerge'] ?? $padBuildMerge;  
 
  if ( $padInclude ) 
    $padBuildMode = 'include';

  $padBuildNow = substr     ( padApp, 0, -1);  
  $padBuildPos = strrpos    ( $padBuildNow, '/');  
  $padBuildBas = substr     ( $padBuildNow, 0, $padBuildPos);  
  $padBuildMrg = substr     ( $padBuildNow, $padBuildPos+1) . '/' . $padPage; 
  $padBuildMrg = padExplode ( $padBuildMrg , '/' );

  include 'build/lib.php';  
  include "build/$padBuildMode.php";
  include 'occurrence/start.php';

  $padBuildMode = $padBuildModeSave;
  
?>