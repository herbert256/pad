<?php

 $padSolSave = padSave ();
 $padSolApp  = [];

  foreach ( $GLOBALS as $k => $v )
    if ( padValidStore ($k) )
      $padSolApp [$k] = $GLOBALS [$k];

  for ( $i = 0; $i <= $pad ; $i++ ) {

    $padSolData ['padData']    [$i] = $padData    [$i];
    $padSolData ['padCurrent'] [$i] = $padCurrent [$i];
    $padSolData ['padSetLvl']  [$i] = $padSetLvl  [$i];
    $padSolData ['padSetOcc']  [$i] = $padSetOcc  [$i];
    $padSolData ['padTable']   [$i] = $padTable   [$i];
    $padSolData ['padPrm']     [$i] = $padPrm     [$i];
    $padSolData ['padOpt']     [$i] = $padOpt     [$i];

  }
  
?>