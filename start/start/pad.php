<?php

  global $padStrZZZ;

  $padStrZZZ [$padStrCnt] = [];

  foreach ( $GLOBALS as $padStrKey => $padStrVal )
    if ( padStrPad ( $padStrKey ) )
      $padStrZZZ [$padStrCnt] [$padStrKey] = $padStrVal;

?>