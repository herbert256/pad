<?php

  if ( ! $padInfoXmlParms )
    return;
  
  $padInfoXmlLvl = $padInfoXmlLevel [$pad];
  $padInfoXmlOcc = $padOccur    [$pad];

  foreach ( $padOpt [$pad] as $padK => $padV )
    if ( $padK and $padV )
      $padInfoXmlTree [$padInfoXmlLvl] ['parms'] ['opt'] [$padK] = $padV;

  foreach ( $padPrm [$pad] as $padK => $padV )
    $padInfoXmlTree [$padInfoXmlLvl] ['parms'] ['prm'] [$padK] = $padV;
  
  foreach ( $padSetLvl [$pad] as $padK => $padV )
    $padInfoXmlTree [$padInfoXmlLvl] ['parms'] ['lvl'] [$padK] = $padV;
  
  foreach ( $padSetOcc [$pad] as $padK => $padV )
    $padInfoXmlTree [$padInfoXmlLvl] ['parms'] ['occ'] [$padK] = $padV;
  
?>