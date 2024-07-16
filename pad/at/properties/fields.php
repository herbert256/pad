<?php

  $padReturn = [];
  $padI      = 0;

  foreach ($GLOBALS ['padCurrent'] [$padIdx] as $padK => $padV) {
    $padI++;
    $padReturn [$padI] ['name']  = $padK;
    $padReturn [$padI] ['value'] = $padV;
  }

  return $padReturn;

?>