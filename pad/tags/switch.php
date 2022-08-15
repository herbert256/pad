<?php

  if ( isset( $padSwNow[$padPrms[$pad]]) )
    $padSwNow  [$padPrms[$pad]]++;
  else {
    $padSwNow  [$padPrms[$pad]] = 0;
    $padSwVars [$padPrms[$pad]] = array_values($padPrmsVal [$pad]);
  }
   
  return $padSwVars [$padPrms[$pad]] [ $padSwNow [$padPrms[$pad]] % count($padPrmsVal [$pad]) ];

?>