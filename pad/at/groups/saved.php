<?php

  global $padSaveLvl, $padSaveOcc;

  for ( $padLoop=$pad; $padLoop; $padLoop-- ) {

    $padIdx = $padLoop + $cor;

    $current = padAtSearch ( $padSaveOcc [$padIdx], $names ); 
    if ( $current !== INF ) 
      return $current;

    $current = padAtSearch ( $padSaveLvl [$padIdx], $names ); 
    if ( $current !== INF ) 
      return $current;

  }

  return INF;

?>