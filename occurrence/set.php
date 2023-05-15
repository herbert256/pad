<?php

  $padSetTmp = [];

  if ( padIsDefaultData ($padData [$pad]) ) 
    $padSetTmp [ $padName [$pad] ] = $padOpt [$pad] [1];
  else
    $padSetTmp [ $padName [$pad] ] = $padCurrent [$pad];

  foreach ( $padCurrent [$pad] as $padK => $padV )
    $padSetTmp [$padK] = $padV;

  foreach ( $padTable [$pad] as $padK => $padV)
    foreach ( $padV as $padK2 => $padV2)
      $padSetTmp [$padK2] = $padV2;
  
  foreach ( $padTable [$pad] as $padK => $padV)
    if ( ! isset($GLOBALS [$padK] ) )
      $padSetTmp [$padK] = $padV;

  foreach ( $padSet [$pad] as $padK => $padV )
    $padSetTmp [$padK] = $padV;

  foreach ( $padSetTmp as $padK => $padV )
    padSetGlobal ( $padK, $padV );


?>