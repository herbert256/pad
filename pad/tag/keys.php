<?php

  $padReturn = [];

  foreach ( $GLOBALS['padData'] [$padIdx] as $padK => $padV) {
    $padReturn [$padK] ['name'] = $padK;
    $padReturn [$padK] ['value'] = padDataForcePad ($padV);
  }

  return $padReturn;

?>