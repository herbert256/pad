<?php

  $padSetTmp = [];

  if ( $padOpt [$pad] [1] )
    $padSetTmp [ $padName [$pad] ] = $padOpt [$pad] [1];

  foreach ( $padBanaanAdd [$pad] as $padK => $padV )
    $padSetTmp [$padK] = $padV;

  foreach ( $padTable [$pad] as $padK => $padV)
    foreach ( $padV as $padK2 => $padV2)
      $padSetTmp [$padK2] = $padV2;
  
  foreach ( $padTable [$pad] as $padK => $padV)
    if ( ! isset($GLOBALS [$padK] ) )
      $padSetTmp [$padK] = $padV;

  foreach ( $padSetOcc [$pad] as $padK => $padV )
    $padCurrent [$pad] [$padK] = padVarOpts ( '', padExplode($padV, '|') );

  foreach ( $padCurrent [$pad] as $padK => $padV )
    $padSetTmp [$padK] = $padV;

  foreach ( $padSetTmp as $padK => $padV )
    padSetGlobalOcc ( $padK, $padV );

  if ( count ( $GLOBALS ['padBanaan'] ) ) {

    foreach ( $padSetTmp as $padK => $padV )
      if ( padValidVar ($padK) )
        $$padK = $padV;

    foreach ( $padSetLvl [$pad] as $padK => $padV )
      if ( ! isset ($padCurrent [$pad] [$padK]) )
        $padCurrent [$pad] [$padK] = $padV;

    foreach ( $padBanaanAdd [$pad] as $padK => $padV )
      if ( ! isset ($padCurrent [$pad] [$padK]) )
        $padCurrent [$pad] [$padK] = $padV;

  }

?>