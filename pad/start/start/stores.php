<?php

  global $padStrStoDat;

  $padStrStoDat [$padStrCnt] = [];

  foreach ( padStrSto as $padStrVal )
    if ( isset ( $GLOBALS [$padStrVal] ))   
      $padStrStoDat [$padStrCnt] [$padStrVal] = $GLOBALS [$padStrVal];

?>