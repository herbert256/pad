<?php

  $padExtPag  = padPageGetName ();
  $padExtIncl = ( padTagParm ( 'include' ) ) ? '&padInclude' : '';
  $padExtQry  = "$padExtIncl";

  foreach ( $padSetLvl [$pad] as $padK => $padV )
    $padExtQry .= "&$padK=" . urlencode($padV);

      if ( $padExtTyp == 'ajax' ) return padPageAjax ( $padExtPag, $padExtQry ); 
  elseif ( $padExtTyp == 'get'  ) return padPageGet  ( $padExtPag, $padExtQry );
   
?>