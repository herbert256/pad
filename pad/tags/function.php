<?php

  if ( $padWalk [$pad] == 'start' and $padPrmsType [$pad] == 'close' ) {
    $padWalk [$pad] = 'end';
    return TRUE;
  }

  $padTag [$pad] = $padPrm [$pad];

  if ( isset ($padPrmsTag [$pad] ['type'] ) )                     
    $padFunctionType = "function_" . $padPrmsTag [$pad] ['type'];                
  else
    $padFunctionType = "function_" . padFunctionType ($padPrm [$pad]);

  $padFunctionVal = $padPrmsVal [$pad];
  unset ( $padFunctionVal [ array_key_first ($padFunctionVal) ] );

  return padFunctionInTag ( $padFunctionType, $padPrm [$pad], $padContent, $padFunctionVal );

?>