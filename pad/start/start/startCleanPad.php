<?php

  global $padStrClnPad;

  $padStrClnPad [$padStrCnt] = [];

  foreach ( $GLOBALS as $padStrKey => $padStrVal )
    if ( padStrClnFld ( $padStrKey ) )
      $padStrClnPad [$padStrCnt] [$padStrKey] = $padStrVal;

?>