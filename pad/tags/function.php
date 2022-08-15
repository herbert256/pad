<?php

  if ( $padWalk [$pad] == 'start' and $padPrmsType [$pad] == 'close' ) {
    $padWalk [$pad] = 'end';
    return TRUE;
  }

  $padTag [$pad] = $padPrm [$pad];

  if ( isset ($padPrmsTag [$pad] ['type'] ) )                     
    $padFunction_type = "function_" . $padPrmsTag [$pad] ['type'];                
  else
    $padFunction_type = "function_" . padFunctionType ($padPrm [$pad]);

  $padFunction_val = $padPrmsVal [$pad];
  unset ( $padFunction_val [ array_key_first ($padFunction_val) ] );

  return padFunctionInTag ( $padFunction_type, $padPrm [$pad], $padContent, $padFunction_val );

?>