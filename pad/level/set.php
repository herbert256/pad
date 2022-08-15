<?php

  if ( ! padValid ($padSetName) )
    return padError ("{set} syntax error (1)");

  if ( $padTag [$pad] <> 'set' or $padPair [$pad] )
    if ( isset($GLOBALS [$padSetName]) )
      $padSetSave [$pad] [$padSetName] = $GLOBALS [$padSetName];
    else
      $padSetDelete [$pad] [] = $padSetName;

  $GLOBALS [$padSetName] = padVarOpts ( '', padExplode($padSetValue, '|') );
  
?>