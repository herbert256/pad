<?php

  $padSaveVarsAssemble [$pad] = $padDeleteVarsAssemble [$pad] = [];

  foreach ( $padSetLvl [$pad] as $padK => $padV ) {

    if ( array_key_exists($padK, $GLOBALS) and ! array_key_exists ($padK, $padSaveVarsAssemble [$pad]) )
      $padSaveVarsAssemble [$pad] [$padK] = $GLOBALS [$padK];

    if ( ! array_key_exists ($padK,  $GLOBALS) )
      $padDeleteVarsAssemble [$pad] [] = $padK;
    else
      unset ( $GLOBALS [$padK] );

    $GLOBALS [$padK] = $padV;

  }
      
  $padSetLvL [$pad] = [];

?>