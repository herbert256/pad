<?php

  global $padData;

  $padReturn = [];

  foreach ( $padData [$padIdx] as $padK => $padV) {
    $padReturn [$padK] ['name'] = $padK;
    $padReturn [$padK] ['value'] = padDataForcePad ($padV);
  }

  return $padReturn;

?>
