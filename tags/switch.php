<?php

  $padSw = $padPrm [$pad] [0];

  if ( isset ( $padSwNow [$padSw] ) ) 
    $padSwNow [$padSw]++;
  else
    $padSwNow [$padSw] = 0;

  $padSwIdx = $padSwNow [$padSw] % $padPrm [$pad] ['_parms_'] + 1 ;

  return $padPrm [$pad] [$padSwIdx];

?>