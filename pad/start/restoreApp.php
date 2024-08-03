<?php

  foreach ( $GLOBALS as $padStrKey => $padStrVal ) 
    if ( padValidStore ( $padStrKey ) ) 
      unset ( $GLOBALS [$padStrKey] );

  foreach ( $padStrApp [$padStrCnt] as $padStrKey => $padStrVal ) 
    $GLOBALS [$padStrKey] = $padStrVal;

?>