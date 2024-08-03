<?php

  global $padStrSave, $padStrApp, $padStrDat, $padStrCnt;

  if ( ! isset ( $padStrCnt ) ) 
    $padStrCnt = 0;
  else
    $padStrCnt++;

  if ( $padStrIso ) 
    $padStrBox = TRUE;

  if ( $padStrBox ) {
    include pad . 'start/backupPad.php';
    include pad . 'start/backupApp.php';
  }

  $padStrSave [$padStrCnt] = padSave ();

?>