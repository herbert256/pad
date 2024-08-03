<?php

  if ( isset ( $GLOBALS ['padStrCod'] ) )
    unset ( $GLOBALS ['padStrCod'] );

  $GLOBALS ['padStrFun'] = TRUE;

  if ( ! isset ( $GLOBALS ['padStrFunCnt'] ) ) 
    $GLOBALS ['padStrFunCnt'] = 1;
  else
    $GLOBALS ['padStrFunCnt']++;

  foreach ( $GLOBALS as $padStrKey => $padStrVal )
    global $$padStrKey;

  $padStrFunCntResult =  include pad . 'start/pad.php'; 

  $GLOBALS ['padStrFunCnt']--;

  if ( ! $GLOBALS ['padStrFunCnt'] )
    unset ( $GLOBALS ['padStrFunCnt'] );  

  return $padStrFunCntResult;

?>