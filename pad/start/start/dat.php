<?php

  global $padStrSav;

  for ( $padStrIdx = 0; $padStrIdx < $pad ; $padStrIdx++ ) 
    foreach ( padStrDat as $padStrVal )
      $padStrSav [$padStrCnt] [$padStrVal] [$padStrIdx] = $GLOBALS [$padStrVal] [$padStrIdx];

?>