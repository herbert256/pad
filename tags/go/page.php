<?php

  $padIncludeSave    = $padInclude;
  $padBuildModeSave  = $padBuildMode;
  $padBuildMergeSave = $padBuildMerge;  
  
  $padInclude        = padTagParm ( 'include', $padInclude        );
  $padBuildMode      = padTagParm ( 'mode',    $padBuildMode      );
  $padBuildMerge     = padTagParm ( 'merge',   $padBuildMerge     );

  $padIncludeSet     = $padInclude;
  $padBuildModeSet   = $padBuildMode;
  $padBuildMergeSet  = $padBuildMerge; 

  $padPagPag = padTagParm ( 'page', $padOpt [$pad] [1] );
  $padPagPrm = $padSet [$pad];
  $padPagQry = "&padInclude=$padInclude&padBuildMode=$padBuildMode&padBuildMerge=$padBuildMerge";

      if ( $padPagTyp == 'ajax'    ) $padRet = padPageAjax    ( $padPagPag, $padPagPrm, $padPagQry ); 
  elseif ( $padPagTyp == 'include' ) $padRet = padPageInclude ( $padPagPag, $padPagPrm             ); 
  elseif ( $padPagTyp == 'build'   ) $padRet = padPageBuild   ( $padPagPag, $padPagPrm,            ); 
  elseif ( $padPagTyp == 'nested'  ) $padRet = padPagePad     ( $padPagPag, $padPagPrm,            ); 
  elseif ( $padPagTyp == 'get'     ) $padRet = padPageGet     ( $padPagPag, $padPagPrm, $padPagQry );

  $padInclude    = $padIncludeSave;
  $padBuildMode  = $padBuildModeSave;
  $padBuildMerge = $padBuildMergeSave;  

  return $padRet;
   
?>