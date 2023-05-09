<?php

  $padExtIncl = padTagParm ( 'include', $padInclude        );
  $padExtMode = padTagParm ( 'mode',    $padBuildMode      );
  $padExtMrg  = padTagParm ( 'merge',   $padBuildMerge     );
  $padExtPag  = padTagParm ( 'page',    $padOpt [$pad] [1] );
  
  $padExtQry  = "&padInclude=$padExtIncl&padBuildMode=$padExtMode&padBuildMerge=$$padExtMrg";

  foreach ( $padSet [$pad] as $padK => $padV )
    $padExtQry .= "&$padK=" . urlencode($padV);

      if ( $padExtTyp == 'ajax' ) $padExtRet = padPageAjax ( $padExtPag, $padExtQry ); 
  elseif ( $padExtTyp == 'get'  ) $padExtRet = padPageGet  ( $padExtPag, $padExtQry );

  return $padExtRet;
   
?>