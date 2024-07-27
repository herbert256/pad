<?php

  $padExtPag = $padParm ;
  $padExtQry = '&padInclude';

  foreach ( $padSetLvl [$pad] as $padK => $padV )
    $padExtQry .= "&$padK=" . urlencode($padV);

  return padPageAjax ( $padExtPag, $padExtQry ) ;
 
?>