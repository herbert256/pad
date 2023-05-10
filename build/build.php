<?php

  $padBuildModeSave = $padBuildMode;

  $padInclude    = $padIncludeSet    ?? $_REQUEST['padInclude']    ?? $padInclude;
  $padBuildMode  = $padBuildModeSet  ?? $_REQUEST['padBuildMode']  ?? $padBuildMode;
  $padBuildMerge = $padBuildMergeSet ?? $_REQUEST['padBuildMerge'] ?? $padBuildMerge;  

  if ( $padInclude ) 
    $padBuildMode = 'include';

  $padBuildMrg = padExplode ( "pages/$padPage", '/' );

  include 'lib.php';  
  include "$padBuildMode.php";
  include pad . 'occurrence/start.php';

  $padBuildMode = $padBuildModeSave;

?>