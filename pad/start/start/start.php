<?php

  if ( $padStrCln and $padStrRes ) 
    $padStrBox = TRUE;

  if ( $padStrBox or $padStrCln or $padStrRes ) 
    $padStrHit = TRUE;
  else
    $padStrHit = FALSE;    

  $padStrStr [$padStrCnt] [0] = $padStrBld;
  $padStrStr [$padStrCnt] [1] = $padStrBox; 
  $padStrStr [$padStrCnt] [2] = $padStrCln; 
  $padStrStr [$padStrCnt] [3] = $padStrCod; 
  $padStrStr [$padStrCnt] [4] = $padStrFun; 
  $padStrStr [$padStrCnt] [5] = $padStrRes;
  $padStrStr [$padStrCnt] [6] = $padStrHit;

?>