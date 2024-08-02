<?php

  padRestore ( $padSolSave [$padSolCnt] );

  #$padFunction = $padSolData [$padSolCnt] [0];
  #$padBuild    = $padSolData [$padSolCnt] [1];
  #$padSandbox  = $padSolData [$padSolCnt] [2];
  #$padIsolate  = $padSolData [$padSolCnt] [3];
  #$padGlobals  = $padSolData [$padSolCnt] [5];

  if ( $padIsolate ) {
    include pad . 'start/restorePad.php';
    include pad . 'start/restoreApp.php';
  }

  $padSolCnt--;

?>