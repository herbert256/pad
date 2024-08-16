<?php

  if ( $padPair [$pad] )
    return padError ("{set ...} can not be used as a open/close tag");

  foreach ( $padSetLvl [$pad] as $padSetName => $padSetValue )
    $GLOBALS [$padSetName] = $padSetValue;

  $padSaveLvl [$pad] = $padDeleteLvl [$pad] = [];

  return TRUE;
  
?>