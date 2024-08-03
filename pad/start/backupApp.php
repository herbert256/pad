<?php

  $padStrApp [$padStrCnt] = [];

  foreach ( $GLOBALS as $padStrKey => $padStrVal )
    if ( padValidStore ($padStrKey) )
      $padStrApp [$padStrCnt] [$padStrKey] = $GLOBALS [$padStrKey];
  
?>