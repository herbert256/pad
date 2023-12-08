<?php

  if ( ! $padXmlParms )
    return;
  
  $padXmlLvl = $padXmlLevel [$pad];
  $padXmlOcc = $padOccur    [$pad];

  foreach ( $padOpt [$pad] as $padK => $padV )
    if ( $padK and $padV )
      $padXmlTree [$padXmlLvl] ['parms'] ['opt'] [$padK] = $padV;

  foreach ( $padPrm [$pad] as $padK => $padV )
    $padXmlTree [$padXmlLvl] ['parms'] ['prm'] [$padK] = $padV;
  
  foreach ( $padSetLvl [$pad] as $padK => $padV )
    $padXmlTree [$padXmlLvl] ['parms'] ['lvl'] [$padK] = $padV;
  
  foreach ( $padSetOcc [$pad] as $padK => $padV )
    $padXmlTree [$padXmlLvl] ['parms'] ['occ'] [$padK] = $padV;
  
?>