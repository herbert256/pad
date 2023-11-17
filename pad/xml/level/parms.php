<?php

  $padXmlLvl = $padXmlLevel [$pad];
  $padXmlOcc = $padOccur    [$pad];

  foreach ( $padOpt [$pad] as $padK => $padV )
    if ( $padK and $padV )
      $padXml [$padXmlLvl] ['parms'] ['opt'] [$padK] = $padV;

  foreach ( $padPrm [$pad] as $padK => $padV )
    $padXml [$padXmlLvl] ['parms'] ['prm'] [$padK] = $padV;
  
  foreach ( $padSetLvl [$pad] as $padK => $padV )
    $padXml [$padXmlLvl] ['parms'] ['lvl'] [$padK] = $padV;
  
  foreach ( $padSetOcc [$pad] as $padK => $padV )
    $padXml [$padXmlLvl] ['parms'] ['occ'] [$padK] = $padV;

  foreach ( $padPrm [$pad] as $padK => $padV )
    padXmlXref ( 'options', $padK );

?>