<?php

  for ( $padStrIdx=0; $padStrIdx <=$pad ; $padStrIdx++ ) {

    $padData    [$padStrIdx] = $padStrDat [$padStrCnt] ['padData']    [$padStrIdx];
    $padCurrent [$padStrIdx] = $padStrDat [$padStrCnt] ['padCurrent'] [$padStrIdx];
    $padSetLvl  [$padStrIdx] = $padStrDat [$padStrCnt] ['padSetLvl']  [$padStrIdx];
    $padSetOcc  [$padStrIdx] = $padStrDat [$padStrCnt] ['padSetOcc']  [$padStrIdx];
    $padTable   [$padStrIdx] = $padStrDat [$padStrCnt] ['padTable']   [$padStrIdx];
    $padPrm     [$padStrIdx] = $padStrDat [$padStrCnt] ['padPrm']     [$padStrIdx];
    $padOpt     [$padStrIdx] = $padStrDat [$padStrCnt] ['padOpt']     [$padStrIdx];

    reset ( $padData [$padStrIdx] );

    while ( current ( $padData [$padStrIdx] ) !== false and 
            key ( $padData [$padStrIdx] ) <> $padKey [$padStrIdx] )
      next ( $padData [$padStrIdx] );
  
  }

?>