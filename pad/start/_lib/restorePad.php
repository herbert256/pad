<?php

  for ( $padSolIdx=0; $padSolIdx <=$pad ; $padSolIdx++ ) {

    $padData    [$padSolIdx] = $padSolData [$padSolCnt] ['padData']    [$padSolIdx];
    $padCurrent [$padSolIdx] = $padSolData [$padSolCnt] ['padCurrent'] [$padSolIdx];
    $padSetLvl  [$padSolIdx] = $padSolData [$padSolCnt] ['padSetLvl']  [$padSolIdx];
    $padSetOcc  [$padSolIdx] = $padSolData [$padSolCnt] ['padSetOcc']  [$padSolIdx];
    $padTable   [$padSolIdx] = $padSolData [$padSolCnt] ['padTable']   [$padSolIdx];
    $padPrm     [$padSolIdx] = $padSolData [$padSolCnt] ['padPrm']     [$padSolIdx];
    $padOpt     [$padSolIdx] = $padSolData [$padSolCnt] ['padOpt']     [$padSolIdx];

  }
 
  for ( $padSolIdx=0; $padSolIdx <=$pad ; $padSolIdx++) { 
    reset ( $padData [$padSolIdx] );
    while ( current ( $padData [$padSolIdx] ) !== false and key ( $padData [$padSolIdx] ) <> $padKey [$padSolIdx] )
      next ( $padData [$padSolIdx] );
  }

?>