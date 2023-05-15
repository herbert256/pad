<?php

  $padBuildModeSave = $padBuildMode;

  $padInclude    = $padIncludeSet    ?? $_REQUEST['padInclude']    ?? $padInclude;
  $padBuildMode  = $padBuildModeSet  ?? $_REQUEST['padBuildMode']  ?? $padBuildMode;
  $padBuildMerge = $padBuildMergeSet ?? $_REQUEST['padBuildMerge'] ?? $padBuildMerge;  

  if ( $padInclude ) 
    $padBuildMode = 'include';

  $padBuildNow = substr(padApp, 0, -1);  
  $padBuildPos = strrpos($padBuildNow, '/');  
  $padBuildBas = substr($padBuildNow, 0, $padBuildPos);  
  $padBuildMrg = substr($padBuildNow, $padBuildPos+1) . '/' . $padPage; 
  $padBuildMrg = padExplode ($padBuildMrg , '/' );

  include 'lib.php';  
  include "$padBuildMode.php";
  include pad . 'occurrence/start.php';

  $padBuildMode = $padBuildModeSave;

?>