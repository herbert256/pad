<?php

  $padSetName  = substr($padParmName, 1);
  $padSetValue = $padParmValue;

  if ( ! padValidVar ($padSetName) )
    return padError ("Invalid variable name: $padSetName");

  $padSetValue = padVarOpts ( '', padExplode($padSetValue, '|') );

  $padPrm [$pad] [$padPrmCnt]  = $padSetValue;
  $padPrm [$pad] [$padSetName] = $padSetValue;

  if ( $padTag [$pad] == 'set' and ! $padPair [$pad] )
    $GLOBALS [$padSetName] = $padSetValue;
  else
    $padSet [$pad] [$padSetName] = $padSetValue;

?>