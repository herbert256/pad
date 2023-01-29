<?php

  if ( isset( $padSwNow[$padPrm [$pad] [0]]) )
    $padSwNow  [$padPrm [$pad] [0]]++;
  else {
    $padSwNow  [$padPrm [$pad] [0]] = 0;
    $padSwVars [$padPrm [$pad] [0]] = array_values($padPrm [$pad]);
  }
   
  return $padSwVars [$padPrm [$pad] [0]] [ $padSwNow [$padPrm [$pad] [0]] % count($padPrm [$pad]) ];

?>