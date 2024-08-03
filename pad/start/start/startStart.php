<?php

  global $padStrStr, $padStrPad;

  if ( ! $padStrCln ) {

    $padStrPad [$padStrCnt] = [];

    foreach ( $GLOBALS as $padStrKey => $padStrVal ) 
      if ( padStrClnFld ( $padStrKey ) )
        $padStrPad [$padStrCnt] [$padStrKey] = $padStrVal;

  }

  $padStrStr [$padStrCnt] [0] = $padStrBld;
  $padStrStr [$padStrCnt] [1] = $padStrBox; 
  $padStrStr [$padStrCnt] [2] = $padStrCln; 
  $padStrStr [$padStrCnt] [3] = $padStrCod; 
  $padStrStr [$padStrCnt] [4] = $padStrFun; 
  $padStrStr [$padStrCnt] [5] = $padStrRes;

?>