<?php

  global $padSolSave, $padSolApp, $padSolData, $padSolCnt;

  if ( ! isset ( $padSolCnt ) ) 
    $padSolCnt = 0;
  else
    $padSolCnt++;

  if ( $padIsolate ) 
    $padSandbox = TRUE;

  if ( $padSandbox ) {
    include pad . 'start/sandboxPad.php';
    include pad . 'start/sandboxApp.php';
  }

  $padSolSave [$padSolCnt] = padSave ();

  #$padSolData [$padSolCnt] [0] = $padFunction;
  #$padSolData [$padSolCnt] [1] = $padBuild;
  #$padSolData [$padSolCnt] [2] = $padSandbox;
  #$padSolData [$padSolCnt] [4] = $padIsolate;
  #$padSolData [$padSolCnt] [5] = $padGlobals;

?>