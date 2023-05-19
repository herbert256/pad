<?php

  $padExtPag  = padPageGetName ();
  $padExtIncl = ( padTagParm ( 'include' ) ) ? '&padInclude' : '';
  $padExtMode = padTagParm ( 'mode',  $padBuildMode  );
  $padExtMrg  = padTagParm ( 'merge', $padBuildMerge );  
  $padExtQry  = "$padExtIncl&padBuildMode=$padExtMode&padBuildMerge=$padExtMrg";

  foreach ( $padSet [$pad] as $padK => $padV )
    $padExtQry .= "&$padK=" . urlencode($padV);

      if ( $padExtTyp == 'ajax' ) return padPageAjax ( $padExtPag, $padExtQry ); 
  elseif ( $padExtTyp == 'get'  ) return padPageGet  ( $padExtPag, $padExtQry );
   
?>