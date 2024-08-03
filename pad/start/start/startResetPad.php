<?php

  global $padStrResPad;

  for ( $padStrIdx = 0; $padStrIdx <= $pad ; $padStrIdx++ ) {

    $padStrResPad [$padStrCnt] ['padData']    [$padStrIdx] = $padData    [$padStrIdx];
    $padStrResPad [$padStrCnt] ['padCurrent'] [$padStrIdx] = $padCurrent [$padStrIdx];
    $padStrResPad [$padStrCnt] ['padSetLvl']  [$padStrIdx] = $padSetLvl  [$padStrIdx];
    $padStrResPad [$padStrCnt] ['padSetOcc']  [$padStrIdx] = $padSetOcc  [$padStrIdx];
    $padStrResPad [$padStrCnt] ['padTable']   [$padStrIdx] = $padTable   [$padStrIdx];
    $padStrResPad [$padStrCnt] ['padPrm']     [$padStrIdx] = $padPrm     [$padStrIdx];
    $padStrResPad [$padStrCnt] ['padOpt']     [$padStrIdx] = $padOpt     [$padStrIdx];

    $padData    [$padStrCnt] = [];
    $padCurrent [$padStrCnt] = [];
    $padSetLvl  [$padStrCnt] = [];
    $padSetOcc  [$padStrCnt] = [];
    $padTable   [$padStrCnt] = [];
    $padPrm     [$padStrCnt] = [];
    $padOpt     [$padStrCnt] = [];

  }

  foreach ( $padStrSto as $padStrVal )
    if ( isset ( $GLOBALS [$padStrVal] ))   
      $GLOBALS [$padStrVal] = [];
   
  unset ( $padSqlConnect );

?>