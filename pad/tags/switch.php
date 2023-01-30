<?php

  $padSw = $padPrm [$pad] [0];

  if ( isset( $padSwNow[$padSw]) ) 

    $padSwNow [$padSw]++;

  else {

    $padSwNow [$padSw] = 0;

    $padSwTmp = array_values ( $padPrm [$pad] );
    unset ( $padSwTmp [0] );
    $padSwVars [$padSw] = array_values ( $padSwTmp );

  }

  $padSwIdx = $padSwNow [$padSw] % count ( $padSwVars [$padSw] );

  return $padSwVars [$padSw] [$padSwIdx];

?>