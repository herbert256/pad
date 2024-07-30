<?php

  for ( $padSolIdx = 0; $padSolIdx <= $pad ; $padSolIdx++ ) {

    $padSolData [$padSolCnt] ['padData']    [$padSolIdx] = $padData    [$padSolIdx];
    $padSolData [$padSolCnt] ['padCurrent'] [$padSolIdx] = $padCurrent [$padSolIdx];
    $padSolData [$padSolCnt] ['padSetLvl']  [$padSolIdx] = $padSetLvl  [$padSolIdx];
    $padSolData [$padSolCnt] ['padSetOcc']  [$padSolIdx] = $padSetOcc  [$padSolIdx];
    $padSolData [$padSolCnt] ['padTable']   [$padSolIdx] = $padTable   [$padSolIdx];
    $padSolData [$padSolCnt] ['padPrm']     [$padSolIdx] = $padPrm     [$padSolIdx];
    $padSolData [$padSolCnt] ['padOpt']     [$padSolIdx] = $padOpt     [$padSolIdx];

  }
  
?>