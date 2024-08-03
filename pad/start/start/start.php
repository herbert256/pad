<?php

  global $padStrPad, $padStrCnt, $padStrStr;

  if ( ! isset ( $padStrCnt ) ) 
    $padStrCnt = 0;
  else
    $padStrCnt++;

  $padStrPad [$padStrCnt] = [];

  if ( $padStrCln ) {
    include pad . 'start/start/startCleanPad.php';
    include pad . 'start/start/startCleanApp.php';
  }

  if ( $padStrRes ) {
    include pad . 'start/start/startResetPad.php';
    include pad . 'start/start/startResetApp.php';
  }

  foreach ( $padSetLvl [$pad] as $padStrKey => $padStrVal ) {
    $GLOBALS [$padStrKey] = $padStrVal;
    global $$padStrKey;
  }

  foreach ( $GLOBALS as $padStrKey => $padStrVal ) 
    if ( substr($padStrKey, 0, 3) == 'pad' and ! in_array ( $padStrKey, $GLOBALS ['padLevelVars']) )
      if ( ! str_starts_with ( $padStrKey, 'padStr' ) )
        if ( substr($padStrKey, 0, 8) <> 'padTrace' and substr($padStrKey, 0, 6) <> 'padXml' ) 
          if ( substr($padStrKey, 0, 7) <> 'padInfo' and substr($padStrKey, 0, 7) <> 'padXref' ) 
            $padStrPad [$padStrCnt] [$padStrKey] = $padStrVal;

  $padStrStr [$padStrCnt] [0] = $padStrBld;
  $padStrStr [$padStrCnt] [1] = $padStrBox; 
  $padStrStr [$padStrCnt] [2] = $padStrCln; 
  $padStrStr [$padStrCnt] [3] = $padStrCod; 
  $padStrStr [$padStrCnt] [4] = $padStrFun; 
  $padStrStr [$padStrCnt] [5] = $padStrRes;

?>