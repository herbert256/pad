<?php

  for ( $padStrIdx = 0; $padStrIdx <= $pad ; $padStrIdx++ ) {

    $padStrDat [$padStrCnt] ['padData']    [$padStrIdx] = $padData    [$padStrIdx];
    $padStrDat [$padStrCnt] ['padCurrent'] [$padStrIdx] = $padCurrent [$padStrIdx];
    $padStrDat [$padStrCnt] ['padSetLvl']  [$padStrIdx] = $padSetLvl  [$padStrIdx];
    $padStrDat [$padStrCnt] ['padSetOcc']  [$padStrIdx] = $padSetOcc  [$padStrIdx];
    $padStrDat [$padStrCnt] ['padTable']   [$padStrIdx] = $padTable   [$padStrIdx];
    $padStrDat [$padStrCnt] ['padPrm']     [$padStrIdx] = $padPrm     [$padStrIdx];
    $padStrDat [$padStrCnt] ['padOpt']     [$padStrIdx] = $padOpt     [$padStrIdx];

  }
  
?>