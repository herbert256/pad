<?php

  for ( $padStrIdx=0; $padStrIdx <=$pad ; $padStrIdx++ ) {

    $padData    [$padStrIdx] = $padStrResPad [$padStrCnt] ['padData']    [$padStrIdx];
    $padCurrent [$padStrIdx] = $padStrResPad [$padStrCnt] ['padCurrent'] [$padStrIdx];
    $padSetLvl  [$padStrIdx] = $padStrResPad [$padStrCnt] ['padSetLvl']  [$padStrIdx];
    $padSetOcc  [$padStrIdx] = $padStrResPad [$padStrCnt] ['padSetOcc']  [$padStrIdx];
    $padTable   [$padStrIdx] = $padStrResPad [$padStrCnt] ['padTable']   [$padStrIdx];
    $padPrm     [$padStrIdx] = $padStrResPad [$padStrCnt] ['padPrm']     [$padStrIdx];
    $padOpt     [$padStrIdx] = $padStrResPad [$padStrCnt] ['padOpt']     [$padStrIdx];

    reset ( $padData [$padStrIdx] );

    while ( current ( $padData [$padStrIdx] ) !== false and 
            key ( $padData [$padStrIdx] ) <> $padKey [$padStrIdx] )
      next ( $padData [$padStrIdx] );
  
  }
  
?>