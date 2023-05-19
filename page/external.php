<?php

  $padExtPag  = padPageGetName ();
  $padExtIncl = ( padTagParm ( 'include' ) ) ? '&padInclude' : '';
  $padExtMode = padTagParm ( 'mode',  $padBuildMode  );
  $padExtMrg  = padTagParm ( 'merge', $padBuildMerge );  
  $padExtQry  = "$padExtIncl&padBuildMode=$padExtMode&padBuildMerge=$padExtMrg";

  foreach ( $padSet [$pad] as $padK => $padV )
    $padExtQry .= "&$padK=" . urlencode($padV);

  $padExtUrl = "$padGoExt$padExtPag$padExtQry" ;

  if     ( $padExtTyp == 'ajax' ) return padPageAjax ( $padExtUrl ); 
  elseif ( $padExtTyp == 'get'  ) return padPageGet  ( $padExtUrl );
   
?>