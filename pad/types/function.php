<?php

  if ( $padWalk [$pad] == 'start' and $padPrmsType [$pad] == 'close' ) {
    $padWalk [$pad] = 'end';
    return TRUE;
  }
   
  return pFunction_in_tag ( $padType, $padTag [$pad], $padContent, $padPrmsVal [$pad] );

?>