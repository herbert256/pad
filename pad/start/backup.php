<?php

  global $padSolSave, $padSolApp, $padSolData, $padSolCnt;

  if ( ! isset ( $padSolCnt ) ) 
    $padSolCnt = 0;
  else
    $padSolCnt++;

  if ( $padIsolate ) 
    $padSandbox = TRUE;

  if ( $padSandbox ) {
    include pad . 'start/backupPad.php';
    include pad . 'start/backupApp.php';
  }

  $padSolSave [$padSolCnt] = padSave ();

?>