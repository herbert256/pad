<?php

  $padSetTmp = [];

  if ( $pad )
    if ( padIsDefaultData ($padData [$pad]) ) 
      $padSetTmp [ $padName [$pad] ] = $padOpt [$pad] [1];
    else
      $padSetTmp [ $padName [$pad] ] = $padCurrent [$pad];

  foreach ( $padCurrent [$pad] as $padK => $padV )
    $padSetTmp [$padK] = $padV;

  foreach ( $padSet [$pad] as $padK => $padV )
    $padSetTmp [$padK] = $padV;  

  foreach ( $padSetTmp as $padK => $padV )
    padSetGlobal ( $padK, $padV );

?>