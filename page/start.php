<?php

  $padPageInsert       [$pad] = $padPage;
  $padDirInsert        [$pad] = $padDir;
  $padPathInsert       [$pad] = $padPath;
  $padIncludeInsert    [$pad] = $padInclude;
  $padBuildModeInsert  [$pad] = $padBuildMode;
  $padBuildMergeInsert [$pad] = $padBuildMerge;  
 
  $padPage           = padPageGetName ();
  $padDir            = padDir ();  
  $padPath           = padPath ();  
  $padIncludeSet     = padTagParm ( 'include', FALSE          );
  $padBuildModeSet   = padTagParm ( 'mode',    $padBuildMode  );
  $padBuildMergeSet  = padTagParm ( 'merge',   $padBuildMerge );

?>