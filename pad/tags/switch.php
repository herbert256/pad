<?php

  if ( isset( $pSw_now[$pPrms[$p]]) )
    $pSw_now  [$pPrms[$p]]++;
  else {
    $pSw_now  [$pPrms[$p]] = 0;
    $pSw_vars [$pPrms[$p]] = array_values($pPrmsVal [$p]);
  }
   
  return $pSw_vars [$pPrms[$p]] [ $pSw_now [$pPrms[$p]] % count($pPrmsVal [$p]) ];

?>