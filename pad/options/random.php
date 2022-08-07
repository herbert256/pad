<?php

  $pRnd_random = ( $pPrmsTag [$p] ['random'] === TRUE) ? 1 : $pPrmsTag [$p] ['random'];

  $pRnd_temp = [];

  for ($pRnd_i=1; $pRnd_i <= $pRnd_random; $pRnd_i++) { 

    if ( ! count($pData[$p]) ) 
      break;

    $pRnd_rand = rand (1, count($pData[$p]));

    $pRnd_now = 0;
    foreach ( $pData[$p] as $pRnd_key => $pRnd_value ) {
      $pRnd_now++;
      if ( $pRnd_now == $pRnd_rand ) {
        $pRnd_temp [$pRnd_key] = $pData [$p] [$pRnd_key] ;
        unset ( $pData [$p] [$pRnd_key] );
        break;
      }
    }
      
  }

  $pData [$p] = $pRnd_temp;

?>