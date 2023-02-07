<?php

  $padSetTmp = [];

  if ( padIsDefaultData ($padData [$pad]) ) 
    $padSetTmp [ $padName [$pad] ] = $padPrm [$pad] [1];
  else
    $padSetTmp [ $padName [$pad] ] = $padCurrent [$pad];

  foreach ( $padCurrent [$pad] as $padK => $padV )
    $padSetTmp [$padK] = $padV;

  foreach ( $padSet [$pad] as $padK => $padV )
    $padSetTmp [$padK] = $padV;  

  foreach ( $padSetTmp as $padK => $padV )
    if ( padValidVar ($padK) )
      padSetGlobal ( $padK, $padV );

?>