<?php

  $padPageInsert       [$pad] = $padPage;
  $padDirInsert        [$pad] = $padDir;
  $padIncludeInsert    [$pad] = $padInclude;
  $padBuildModeInsert  [$pad] = $padBuildMode;
  $padBuildMergeInsert [$pad] = $padBuildMerge;  
 
  $padPage           = padPageGetName ();
  $padDir            = padDir ($padPage);  
  $padIncludeSet     = padTagParm ( 'include', FALSE          );
  $padBuildModeSet   = padTagParm ( 'mode',    $padBuildMode  );
  $padBuildMergeSet  = padTagParm ( 'merge',   $padBuildMerge );
  
  include 'setup.php';

?>