<?php

  global $padStrClnApp;

  $padStrClnApp [$padStrCnt] = [];

  foreach ( $GLOBALS as $padStrKey => $padStrVal )
    if ( padValidStore ($padStrKey) )
      $padStrClnApp [$padStrCnt] [$padStrKey] = TRUE;

?>