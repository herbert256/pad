<?php

  if ( isset ( $GLOBALS ['padStrCod'] ) )
    unset ( $GLOBALS ['padStrCod'] );

  $GLOBALS ['padStrFun'] = TRUE;
  $GLOBALS ['padStrFunCnt']++;
  $GLOBALS ['padStrFunVar'] [ $GLOBALS ['padStrFunCnt'] ] = [];

  foreach ( $GLOBALS as $padStrKey => $padStrVal )
    global $$padStrKey;

  $padLvlFun [$pad] = $padStrFunCnt;
$GLOBALS['yyy']=1;
  $padStrFunResult = include 'start/pad.php'; 

  if ( ! $padStrBox and ! $padStrCln )
    foreach ( get_defined_vars () as $padStrKey => $padStrVal )
      if ( padValidStore ( $padStrKey ) )
        if ( ! isset ( $GLOBALS [$padStrKey] ) )
          $GLOBALS [$padStrKey] = $padStrVal;

  $padStrFunCnt--;

  return $padStrFunResult;

?>