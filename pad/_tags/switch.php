<?php

  $padSw = $padOpt [$pad] [0];

  if ( isset ( $padSwNow [$padSw] ) ) 
    $padSwNow [$padSw]++;
  else
    $padSwNow [$padSw] = 0;

  $padSwCnt = count ( $padOpt [$pad] ) - 1;
  $padSwIdx = $padSwNow [$padSw] % $padSwCnt + 1 ;

  return $padOpt [$pad] [$padSwIdx];

?>