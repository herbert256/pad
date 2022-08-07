<?php

  if ( isset( $pSw_now[$pPrms]) )
    $pSw_now  [$pPrms]++;
  else {
    $pSw_now  [$pPrms] = 0;
    $pSw_vars [$pPrms] = array_values($pPrmsVal [$p]);
  }
   
  return $pSw_vars [$pPrms] [ $pSw_now [$pPrms] % count($pPrmsVal [$p]) ];

?>