<?php

  $padStrBld = $padStrStr [$padStrCnt] [0];
  $padStrBox = $padStrStr [$padStrCnt] [1]; 
  $padStrCln = $padStrStr [$padStrCnt] [2]; 
  $padStrCod = $padStrStr [$padStrCnt] [3]; 
  $padStrFun = $padStrStr [$padStrCnt] [4]; 
  $padStrRes = $padStrStr [$padStrCnt] [5];
  
   if ( $padStrRes ) {
    include pad . 'start/end/endResetPad.php';
    include pad . 'start/end/endResetApp.php';
  }

  if ( $padStrCln ) {
    include pad . 'start/end/endCleanPad.php';
    include pad . 'start/end/endCleanApp.php';
  }

  foreach ( $padStrPad [$padStrCnt] as $padStrKey => $padStrVal ) 
    $GLOBALS [$padStrKey] = $padStrVal;

  $padStrCnt--;

?>