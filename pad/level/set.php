<?php

  if ( ! pValid ($padSet_name) )
    return pError ("{set} syntax error (1)");

  if ( $padTag [$pad] <> 'set' or $padPair [$pad] )
    if ( isset($GLOBALS [$padSet_name]) )
      $padSet_save [$pad] [$padSet_name] = $GLOBALS [$padSet_name];
    else
      $padSet_delete [$pad] [] = $padSet_name;

  $GLOBALS [$padSet_name] = pVar_opts ( '', pExplode($padSet_value, '|') );
  
?>