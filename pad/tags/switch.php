<?php

  if ( isset( $padSw_now[$padPrms[$pad]]) )
    $padSw_now  [$padPrms[$pad]]++;
  else {
    $padSw_now  [$padPrms[$pad]] = 0;
    $padSw_vars [$padPrms[$pad]] = array_values($padPrmsVal [$pad]);
  }
   
  return $padSw_vars [$padPrms[$pad]] [ $padSw_now [$padPrms[$pad]] % count($padPrmsVal [$pad]) ];

?>