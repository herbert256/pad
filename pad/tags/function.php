<?php

  if ( $padWalk [$pad] == 'start' and $padPrmType [$pad] == 'close' ) {
    $padWalk [$pad] = 'end';
    return TRUE;
  }

  $padTag [$pad] = $padPrm [$pad] [0];

  if ( isset ($padPrm [$pad] ['type'] ) )                     
    $padFunctionType = "function_" . $padPrm [$pad] ['type'];                
  else
    $padFunctionType = "function_" . padFunctionType ($padPrm [$pad] [0]);

  $padFunctionVal = $padPrm [$pad];
  unset ( $padFunctionVal [ array_key_first ($padFunctionVal) ] );

  return padFunctionInTag ( $padFunctionType, $padPrm [$pad] [0], $padContent, $padFunctionVal );

?>