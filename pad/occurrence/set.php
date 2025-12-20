<?php

  $padSetTmp = [];

  if ( $padParm )
    $padSetTmp [ $padName [$pad] ] = $padParm;

  foreach ( $padSetOcc [$pad] as $padK => $padV )
    $padCurrent [$pad] [$padK] = padEval ( $padV );

  foreach ( $padCurrent [$pad] as $padK => $padV )
    $padSetTmp [$padK] = $padV;

  foreach ( $padSetTmp as $padK => $padV )
    padSetGlobalOcc ( $padK, $padV );

?>