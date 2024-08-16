<?php

  global $padSaveLvl, $padSaveOcc;

  $current = padAtSearch ( $padSaveOcc [$padIdx], $names ); 
  if ( $current !== INF ) 
    return $current;

  $current = padAtSearch ( $padSaveLvl [$padIdx], $names ); 
  if ( $current !== INF ) 
    return $current;

  return INF;

?>