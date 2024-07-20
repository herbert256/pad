<?php

  global $pad, $padSaveLvl, $padSaveOcc;

  for ( $padIdx=$pad; $padIdx; $padIdx-- ) {

    $check = padFindNames ( $padSaveLvl [$padIdx], $names );
    if ( $check !== INF ) 
      return $check;

    $check = padFindNames ( $padSaveOcc [$padIdx], $names );
    if ( $check !== INF ) 
      return $check;

  }

  return INF;

?>;