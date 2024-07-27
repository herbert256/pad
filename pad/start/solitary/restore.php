<?php

  foreach ( $GLOBALS as $k => $v ) 
    if ( padValidStore ( $k ) ) 
      unset ( $GLOBALS [$k] );

  foreach ( $padSolApp as $k => $v ) 
    $GLOBALS [$k] = $v;

  padRestore ( $padSolSave );

  for ( $i=0; $i <=$pad ; $i++ ) {

    $padData    [$i] = $padSolData ['padData']    [$i];
    $padCurrent [$i] = $padSolData ['padCurrent'] [$i];
    $padSetLvl  [$i] = $padSolData ['padSetLvl']  [$i];
    $padSetOcc  [$i] = $padSolData ['padSetOcc']  [$i];
    $padTable   [$i] = $padSolData ['padTable']   [$i];
    $padPrm     [$i] = $padSolData ['padPrm']     [$i];
    $padOpt     [$i] = $padSolData ['padOpt']     [$i];

  }
 
  for ( $i=0; $i <=$pad ; $i++) { 
    reset ( $padData [$i] );
    while ( current ( $padData [$i] ) !== false and key ( $padData [$i] ) <> $padKey [$i] )
      next ( $padData [$i] );
  }

?>