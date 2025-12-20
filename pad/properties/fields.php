<?php

  global $padCurrent;

  $padReturn = [];
  $padI      = 0;

  foreach ($padCurrent [$padIdx] as $padK => $padV) {
    $padI++;
    $padReturn [$padI] ['name']  = $padK;
    $padReturn [$padI] ['value'] = $padV;
  }

  return $padReturn;

?>
