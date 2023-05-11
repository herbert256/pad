<?php

  $padPageInsert [$pad]       = $padPage;
  $padIncludeInsert [$pad]    = $padInclude;
  $padBuildModeInsert [$pad]  = $padBuildMode;
  $padBuildMergeInsert [$pad] = $padBuildMerge;  
 
  $padPage           = padPageGetName ();
  
  $padIncludeSet     = padTagParm ( 'include', $padInclude        );
  $padBuildModeSet   = padTagParm ( 'mode',    $padBuildMode      );
  $padBuildMergeSet  = padTagParm ( 'merge',   $padBuildMerge     );
  
  include 'setup.php';

?>