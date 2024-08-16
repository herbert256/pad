<?php

  if ( ! $padInfXmlParms )
    return;
  
  $padInfXmlLvl = $padInfXmlLevel [$pad];
  $padInfXmlOcc = $padOccur    [$pad];

  foreach ( $padOpt [$pad] as $padK => $padV )
    if ( $padK and $padV )
      $padInfXmlTree [$padInfXmlLvl] ['parms'] ['opt'] [$padK] = $padV;

  foreach ( $padPrm [$pad] as $padK => $padV )
    $padInfXmlTree [$padInfXmlLvl] ['parms'] ['prm'] [$padK] = $padV;
  
  foreach ( $padSetLvl [$pad] as $padK => $padV )
    $padInfXmlTree [$padInfXmlLvl] ['parms'] ['lvl'] [$padK] = $padV;
  
  foreach ( $padSetOcc [$pad] as $padK => $padV )
    $padInfXmlTree [$padInfXmlLvl] ['parms'] ['occ'] [$padK] = $padV;
  
?>