<?php

  if ( $padWalk [$pad] == 'start' and $padPrmsType [$pad] == 'close' ) {
    $padWalk [$pad] = 'end';
    return TRUE;
  }
   
  return padFunctionInTag ( $padType, $padTag [$pad], $padContent, $padPrmsVal [$pad] );

?>