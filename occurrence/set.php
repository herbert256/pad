<?php

  $padSetTmp = [];

  if ( $padParm )
    $padSetTmp [ $padName [$pad] ] = $padParm;

  foreach ( $padTable [$pad] as $padK => $padV)
    foreach ( $padV as $padK2 => $padV2)
      $padSetTmp [$padK2] = $padV2;
  
  foreach ( $padTable [$pad] as $padK => $padV)
    if ( ! isset($GLOBALS [$padK] ) )
      $padSetTmp [$padK] = $padV;

  foreach ( $padSetOcc [$pad] as $padK => $padV )
    $padCurrent [$pad] [$padK] = padEval ( $padV );

  foreach ( $padCurrent [$pad] as $padK => $padV )
    $padSetTmp [$padK] = $padV;

  foreach ( $padSetTmp as $padK => $padV )
    padSetGlobalOcc ( $padK, $padV );

?>