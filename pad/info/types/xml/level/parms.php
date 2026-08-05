<?php

  // Copies a level's parameters into its node for the 'xml' info mode, from events/parms.php
  // once the tag has been parsed, and only when $padInfoXmlParms is set.
  //
  // Four kinds are kept apart, and the renderer turns them into <parm type="..."> lines:
  // opt (the tag's options), prm (its named parameters), lvl (level variables set on the tag,
  // $var) and occ (occurrence variables, %var).

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