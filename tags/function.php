<?php

  if ( $padWalk [$pad] == 'start' and $padPrmType [$pad] == 'close' ) {
    $padWalk [$pad] = 'end';
    return TRUE;
  }

  $padTag [$pad] = $padOpt [$pad] [1];

  if ( isset ($padPrm [$pad] ['type'] ) )                     
    $padFunctionType = "function_" . $padPrm [$pad] ['type'];                
  else
    $padFunctionType = "function_" . padFunctionType ($padOpt [$pad] [1]);

  $padFunctionVal = $padPrm [$pad];
  unset ( $padFunctionVal [ array_key_first ($padFunctionVal) ] );

  return padFunctionInTag ( $padFunctionType, $padOpt [$pad] [1], $padContent, $padFunctionVal );

?>