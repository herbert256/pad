<?php

  $padExtPag = $padParm ;
  $padExtQry = '&padInclude';

  foreach ( $padSetLvl [$pad] as $padK => $padV )
    $padExtQry .= "&$padK=" . urlencode($padV);

  return padEscape ( padPageGet ( $padExtPag, $padExtQry ) );
 
?>