<?php

  // Publishes the current row as occurrence variables, so {$field} resolves inside the
  // loop body.
  //
  // The tag parameter is published under the tag's own name first, then the level's %var
  // assignments ($padSetOcc) are evaluated into the row, then every field is set through
  // padSetGlobalOcc - which saves whatever it shadows so padResetOcc can put it back when
  // the occurrence ends.

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