<?php

  if ( isset ( $GLOBALS ['padStrCod'] ) )
    unset ( $GLOBALS ['padStrCod'] );

  $GLOBALS ['padStrFun'] = TRUE;

  foreach ( $GLOBALS as $padStrKey => $padStrVal )
    global $$padStrKey;

  $padStrFunCnt++;

  global $padStrFunVar;
  $padStrFunVar [$padStrFunCnt] = [];

  $padStrFunCntResult = include pad . 'start/pad.php'; 

  $padStrFunCnt--;

  return $padStrFunCntResult;

?>