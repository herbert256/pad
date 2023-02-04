<?php

  if ( $padWalk [$pad] == 'start' and $padPrmType [$pad] == 'close' ) {
    $padWalk [$pad] = 'end';
    return TRUE;
  }
   
  return padFunctionInTag ( $padType, $padTag [$pad], $padContent, $padPrm [$pad] );

?>